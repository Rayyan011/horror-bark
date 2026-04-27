<?php

namespace App\Services;

use App\Mail\BookingChangedMail;
use App\Mail\BookingConfirmationMail;
use App\Mail\BookingReminderMail;
use App\Models\BeachEvent;
use App\Models\BeachEventBooking;
use App\Models\Ferry;
use App\Models\FerryBooking;
use App\Models\Game;
use App\Models\GameBooking;
use App\Models\HotelBooking;
use App\Models\Ride;
use App\Models\RideBooking;
use App\Models\User;
use App\Support\BookingSupport;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Throwable;

class BookingLifecycleService
{
    public function __construct(
        private readonly InvoiceService $invoiceService,
        private readonly FerryPassService $ferryPassService,
        private readonly IslandAccessService $islandAccessService,
        private readonly AuditLogger $auditLogger
    ) {}

    public function createConfirmedBooking(Model $booking, User $actor): Model
    {
        $booking = DB::transaction(function () use ($booking, $actor) {
            if (! $booking->invoice) {
                $this->invoiceService->createForBooking($booking, $actor->id, (float) $booking->total_price);
            }

            if ($booking instanceof FerryBooking) {
                $this->ferryPassService->createForBooking($booking);
            }

            $booking->loadMissing($this->relationsFor($booking));

            $this->auditLogger->log(
                $actor,
                'booking.confirmed',
                $booking,
                null,
                $this->snapshot($booking)
            );

            return $booking;
        });

        $this->queueMailSafely(
            $actor->email,
            new BookingConfirmationMail($booking->fresh($this->relationsFor($booking))),
            'booking.confirmed',
            $booking
        );

        return $booking;
    }

    public function cancelBooking(Model $booking, User $actor): void
    {
        if ($booking->status === 'canceled') {
            return;
        }

        $this->ensureSelfServiceWindow($booking);

        $booking = DB::transaction(function () use ($booking, $actor) {
            $before = $this->snapshot($booking);

            $booking->update(['status' => 'canceled']);

            if ($booking->invoice) {
                $invoiceBefore = $booking->invoice->only(['id', 'status', 'amount', 'invoice_number']);
                $booking->invoice->update(['status' => 'canceled']);
                $this->auditLogger->log($actor, 'invoice.canceled', $booking->invoice, $invoiceBefore, $booking->invoice->fresh()->toArray());
            }

            $booking->loadMissing($this->relationsFor($booking));

            $this->auditLogger->log($actor, 'booking.canceled', $booking, $before, $this->snapshot($booking));

            return [$booking->fresh($this->relationsFor($booking)), $before];
        });

        $this->queueMailSafely(
            $actor->email,
            new BookingChangedMail($booking[0], 'canceled', $booking[1]),
            'booking.canceled',
            $booking[0]
        );
    }

    public function rescheduleBooking(Model $booking, array $changes, User $actor): Model
    {
        $this->ensureSelfServiceWindow($booking);

        [$booking, $before] = DB::transaction(function () use ($booking, $changes, $actor) {
            $before = $this->snapshot($booking);
            $updatedAttributes = $this->resolveRescheduleAttributes($booking, $changes, $actor);

            $booking->update($updatedAttributes);

            if ($booking->invoice) {
                $booking->invoice->update(['amount' => (float) $booking->fresh()->total_price]);
                $this->invoiceService->generatePdf($booking->invoice->fresh());
            }

            if ($booking instanceof FerryBooking) {
                $this->ferryPassService->generatePdf($booking->fresh());
                $this->auditLogger->log($actor, 'ferry_pass.regenerated', $booking->fresh(), null, [
                    'pass_number' => $booking->fresh()->pass_number,
                    'pass_path' => $booking->fresh()->pass_path,
                ]);
            }

            $booking->loadMissing($this->relationsFor($booking));

            $this->auditLogger->log($actor, 'booking.rescheduled', $booking, $before, $this->snapshot($booking));

            return [$booking->fresh($this->relationsFor($booking)), $before];
        });

        $this->queueMailSafely(
            $actor->email,
            new BookingChangedMail($booking, 'rescheduled', $before),
            'booking.rescheduled',
            $booking
        );

        return $booking;
    }

    public function sendReminder(Model $booking): void
    {
        if ($booking->status !== 'confirmed' || $booking->reminder_sent_at) {
            return;
        }

        $booking->loadMissing($this->relationsFor($booking));

        $this->queueMailSafely(
            $booking->user->email,
            new BookingReminderMail($booking),
            'booking.reminder_sent',
            $booking
        );
        $booking->update(['reminder_sent_at' => now()]);

        $this->auditLogger->log($booking->user, 'booking.reminder_sent', $booking, null, [
            'start_at' => BookingSupport::startAt($booking)->toIso8601String(),
        ]);
    }

    public function ensureSelfServiceWindow(Model $booking): void
    {
        if (! BookingSupport::canSelfServiceChange($booking)) {
            throw ValidationException::withMessages([
                'booking' => 'This booking is outside the 24-hour self-service change window.',
            ]);
        }
    }

    private function resolveRescheduleAttributes(Model $booking, array $changes, User $actor): array
    {
        return match (true) {
            $booking instanceof HotelBooking => $this->rescheduleHotel($booking, $changes),
            $booking instanceof FerryBooking => $this->rescheduleFerry($booking, $changes, $actor),
            $booking instanceof RideBooking => $this->rescheduleRide($booking, $changes, $actor),
            $booking instanceof GameBooking => $this->rescheduleGame($booking, $changes, $actor),
            $booking instanceof BeachEventBooking => $this->rescheduleBeachEvent($booking, $changes, $actor),
            default => throw ValidationException::withMessages(['booking' => 'Unsupported booking type.']),
        };
    }

    private function rescheduleHotel(HotelBooking $booking, array $changes): array
    {
        $startDate = Carbon::parse($changes['start_date'])->startOfDay();
        $endDate = Carbon::parse($changes['end_date'])->startOfDay();

        if ($startDate->lt(Carbon::today())) {
            throw ValidationException::withMessages([
                'start_date' => 'Choose today or a future check-in date.',
            ]);
        }

        if ($endDate->lessThanOrEqualTo($startDate)) {
            throw ValidationException::withMessages([
                'end_date' => 'Choose a check-out date after check-in.',
            ]);
        }

        $overlappingQuantity = HotelBooking::query()
            ->where('room_id', $booking->room_id)
            ->where('id', '!=', $booking->id)
            ->where('status', '!=', 'canceled')
            ->where('start_date', '<', $endDate)
            ->where('end_date', '>', $startDate)
            ->sum('quantity');

        if ($overlappingQuantity + $booking->quantity > $booking->room->max_occupancy) {
            throw ValidationException::withMessages([
                'start_date' => 'Not enough availability for the selected dates.',
            ]);
        }

        $nights = max(1, $startDate->diffInDays($endDate));

        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_price' => $booking->room->price * $booking->quantity * $nights,
            'reminder_sent_at' => null,
        ];
    }

    private function rescheduleFerry(FerryBooking $booking, array $changes, User $actor): array
    {
        $bookingTime = Carbon::parse($changes['booking_time'])->setSecond(0);
        $hour = (int) $bookingTime->format('G');

        if ($bookingTime->lte(Carbon::now())) {
            throw ValidationException::withMessages([
                'booking_time' => 'Choose a future ferry time.',
            ]);
        }

        if ($bookingTime->minute !== 0 || $hour < 9 || $hour > 16) {
            throw ValidationException::withMessages([
                'booking_time' => 'Ferry bookings must start on the hour between 9:00 and 16:00.',
            ]);
        }

        /** @var Ferry $ferry */
        $ferry = $booking->ferry()->with('island')->firstOrFail();

        if (
            $this->islandAccessService->ferryRequiresHotel($ferry)
            && ! $this->islandAccessService->hasConfirmedHotelStayForFerryAt($actor, $bookingTime)
        ) {
            throw ValidationException::withMessages([
                'booking_time' => $this->islandAccessService->hotelStayAccessError(
                    $actor,
                    'A confirmed hotel stay is required before booking this Horror Island ferry.',
                    'Choose a ferry time during your confirmed hotel stay',
                    includeCheckoutDay: true,
                ),
            ]);
        }

        $bookedQuantity = FerryBooking::query()
            ->where('ferry_id', $booking->ferry_id)
            ->where('id', '!=', $booking->id)
            ->where('booking_time', $bookingTime)
            ->where('status', '!=', 'canceled')
            ->sum('quantity');

        if ($bookedQuantity + $booking->quantity > $ferry->max_capacity) {
            throw ValidationException::withMessages([
                'booking_time' => 'Not enough capacity for that time slot.',
            ]);
        }

        return [
            'booking_time' => $bookingTime,
            'reminder_sent_at' => null,
        ];
    }

    private function rescheduleRide(RideBooking $booking, array $changes, User $actor): array
    {
        $bookingTime = Carbon::parse($changes['booking_time'])->setSecond(0);
        $hour = (int) $bookingTime->format('G');

        if ($bookingTime->lte(Carbon::now())) {
            throw ValidationException::withMessages([
                'booking_time' => 'Choose a future ride time.',
            ]);
        }

        if ($bookingTime->minute !== 0 || ! in_array($hour, [9, 17], true)) {
            throw ValidationException::withMessages([
                'booking_time' => 'Ride bookings are only available at 9:00 or 17:00.',
            ]);
        }

        /** @var Ride $ride */
        $ride = $booking->ride()->with('island')->firstOrFail();

        if (
            $this->islandAccessService->rideRequiresHotel($ride)
            && ! $this->islandAccessService->hasConfirmedHotelStayAt($actor, $bookingTime)
        ) {
            throw ValidationException::withMessages([
                'booking_time' => $this->islandAccessService->hotelStayAccessError(
                    $actor,
                    'A confirmed hotel stay is required before booking this ride.',
                    'Choose a ride time during your confirmed hotel stay',
                ),
            ]);
        }

        $bookedQuantity = RideBooking::query()
            ->where('ride_id', $booking->ride_id)
            ->where('id', '!=', $booking->id)
            ->where('booking_time', $bookingTime)
            ->where('status', '!=', 'canceled')
            ->sum('quantity');

        if ($bookedQuantity + $booking->quantity > $ride->max_capacity) {
            throw ValidationException::withMessages([
                'booking_time' => 'Not enough capacity for that time slot.',
            ]);
        }

        return [
            'booking_time' => $bookingTime,
            'reminder_sent_at' => null,
        ];
    }

    private function rescheduleGame(GameBooking $booking, array $changes, User $actor): array
    {
        $bookingTime = Carbon::parse($changes['booking_time'])->setSecond(0);
        $hour = (int) $bookingTime->format('G');

        if ($bookingTime->lte(Carbon::now())) {
            throw ValidationException::withMessages([
                'booking_time' => 'Choose a future game time.',
            ]);
        }

        if ($bookingTime->minute !== 0 || ! in_array($hour, [9, 17], true)) {
            throw ValidationException::withMessages([
                'booking_time' => 'Game bookings are only available at 9:00 or 17:00.',
            ]);
        }

        /** @var Game $game */
        $game = $booking->game()->with('island')->firstOrFail();

        if (
            $this->islandAccessService->gameRequiresHotel($game)
            && ! $this->islandAccessService->hasConfirmedHotelStayAt($actor, $bookingTime)
        ) {
            throw ValidationException::withMessages([
                'booking_time' => $this->islandAccessService->hotelStayAccessError(
                    $actor,
                    'A confirmed hotel stay is required before booking this game.',
                    'Choose a game time during your confirmed hotel stay',
                ),
            ]);
        }

        $bookedQuantity = GameBooking::query()
            ->where('game_id', $booking->game_id)
            ->where('id', '!=', $booking->id)
            ->where('booking_time', $bookingTime)
            ->where('status', '!=', 'canceled')
            ->sum('quantity');

        if ($bookedQuantity + $booking->quantity > $game->max_capacity) {
            throw ValidationException::withMessages([
                'booking_time' => 'Not enough capacity for that time slot.',
            ]);
        }

        return [
            'booking_time' => $bookingTime,
            'reminder_sent_at' => null,
        ];
    }

    private function rescheduleBeachEvent(BeachEventBooking $booking, array $changes, User $actor): array
    {
        /** @var BeachEvent $beachEvent */
        $beachEvent = $booking->beachEvent()->with('island')->firstOrFail();

        $bookingDate = Carbon::parse($changes['booking_date'])->toDateString();

        if (Carbon::parse($bookingDate)->lt(Carbon::today())) {
            throw ValidationException::withMessages([
                'booking_date' => 'Choose today or a future event date.',
            ]);
        }

        if ($bookingDate !== $beachEvent->event_date->toDateString()) {
            throw ValidationException::withMessages([
                'booking_date' => 'Booking date must match the event date.',
            ]);
        }

        $bookingTime = Carbon::parse($bookingDate.' '.$changes['booking_time'])->setSecond(0);

        if ($bookingTime->lte(Carbon::now())) {
            throw ValidationException::withMessages([
                'booking_time' => 'Choose a future event time.',
            ]);
        }

        if (
            $this->islandAccessService->beachEventRequiresHotel($beachEvent)
            && ! $this->islandAccessService->hasConfirmedHotelStayAt($actor, $bookingTime)
        ) {
            throw ValidationException::withMessages([
                'booking_time' => $this->islandAccessService->hotelStayAccessError(
                    $actor,
                    'A confirmed hotel stay covering this event date is required before booking.',
                    'Choose an event time during your confirmed hotel stay',
                ),
            ]);
        }

        $bookedQuantity = BeachEventBooking::query()
            ->where('beach_event_id', $booking->beach_event_id)
            ->where('id', '!=', $booking->id)
            ->where('booking_date', $bookingDate)
            ->where('booking_time', $bookingTime)
            ->where('status', '!=', 'canceled')
            ->sum('quantity');

        if ($bookedQuantity + $booking->quantity > $beachEvent->max_capacity) {
            throw ValidationException::withMessages([
                'booking_time' => 'Not enough capacity for that time slot.',
            ]);
        }

        return [
            'booking_date' => $bookingDate,
            'booking_time' => $bookingTime,
            'reminder_sent_at' => null,
        ];
    }

    private function snapshot(Model $booking): array
    {
        $booking->loadMissing($this->relationsFor($booking));

        return [
            'status' => $booking->status,
            'quantity' => $booking->quantity,
            'total_price' => (float) $booking->total_price,
            'schedule' => BookingSupport::scheduleLabel($booking),
            'type' => BookingSupport::typeKey($booking),
        ];
    }

    private function relationsFor(Model $booking): array
    {
        return match (true) {
            $booking instanceof HotelBooking => ['room.hotel', 'invoice', 'user'],
            $booking instanceof FerryBooking => ['ferry.island', 'invoice', 'user'],
            $booking instanceof RideBooking => ['ride.island', 'invoice', 'user'],
            $booking instanceof GameBooking => ['game.island', 'invoice', 'user'],
            $booking instanceof BeachEventBooking => ['beachEvent.island', 'invoice', 'user'],
            default => ['invoice', 'user'],
        };
    }

    private function queueMailSafely(string $recipient, Mailable $mailable, string $context, Model $booking): void
    {
        try {
            Mail::to($recipient)->queue($mailable);
        } catch (Throwable $exception) {
            Log::warning('Booking mail queue failed.', [
                'context' => $context,
                'recipient' => $recipient,
                'booking_type' => $booking::class,
                'booking_id' => $booking->getKey(),
                'error' => $exception->getMessage(),
            ]);

            report($exception);
        }
    }
}
