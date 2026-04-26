<?php

namespace App\Services;

use App\Models\BeachEvent;
use App\Models\BeachEventBooking;
use App\Models\Ferry;
use App\Models\FerryBooking;
use App\Models\Game;
use App\Models\GameBooking;
use App\Models\HotelBooking;
use App\Models\Ride;
use App\Models\RideBooking;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class BookingCheckoutService
{
    public function __construct(
        private readonly BookingLifecycleService $bookingLifecycleService,
        private readonly IslandAccessService $islandAccessService,
        private readonly PromotionOfferService $promotionOfferService,
    ) {}

    public function prepareHotel(User $user, Room $room, array $input): array
    {
        $data = Validator::make($input, [
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'quantity' => ['required', 'integer', 'min:1', 'max:'.$room->max_occupancy],
            'promotion_id' => ['nullable', 'integer', 'exists:promotions,id'],
        ])->validate();

        $startDate = Carbon::parse($data['start_date'])->startOfDay();
        $endDate = Carbon::parse($data['end_date'])->startOfDay();
        $nights = max(1, $startDate->diffInDays($endDate));

        $overlappingQuantity = HotelBooking::query()
            ->where('room_id', $room->id)
            ->where('status', '!=', 'canceled')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->where('start_date', '<', $endDate)
                    ->where('end_date', '>', $startDate);
            })
            ->sum('quantity');

        if ($overlappingQuantity + $data['quantity'] > $room->max_occupancy) {
            throw ValidationException::withMessages([
                'quantity' => 'Not enough availability for the selected dates.',
            ]);
        }

        $room->loadMissing('hotel');

        $promotion = $this->promotionOfferService->resolveAppliedPromotion(
            $data['promotion_id'] ?? null,
            'hotel',
            $room,
        );
        $baseUnitPrice = (float) $room->price;
        $baseTotalPrice = (float) ($room->price * $data['quantity'] * $nights);
        $summary = [
            'type_label' => 'Hotel Stay',
            'title' => $room->hotel?->name ?? 'Hotel stay',
            'subtitle' => 'Room '.$room->room_number,
            'schedule_label' => $startDate->format('M d, Y').' to '.$endDate->format('M d, Y'),
            'quantity_label' => 'Guests',
            'quantity' => (int) $data['quantity'],
            'unit_label' => 'Nightly rate',
            'unit_price' => $baseUnitPrice,
            'total_price' => $baseTotalPrice,
            'line_items' => [
                ['label' => 'Guests', 'value' => (string) $data['quantity']],
                ['label' => 'Stay', 'value' => $nights.' night'.($nights === 1 ? '' : 's')],
                ['label' => 'Check-in', 'value' => $startDate->format('M d, Y')],
                ['label' => 'Check-out', 'value' => $endDate->format('M d, Y')],
            ],
        ];

        $summary = $this->applyPromotionToSummary($summary, $promotion);

        return [
            'type' => 'hotel',
            'resource_id' => $room->id,
            'booking_data' => [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'quantity' => (int) $data['quantity'],
                'promotion_id' => $promotion['id'] ?? null,
            ],
            'summary' => $summary,
        ];
    }

    public function prepareFerry(User $user, Ferry $ferry, array $input): array
    {
        $data = Validator::make($input, [
            'booking_time' => ['required', 'date'],
            'quantity' => ['required', 'integer', 'min:1', 'max:'.$ferry->max_booking_quantity],
            'promotion_id' => ['nullable', 'integer', 'exists:promotions,id'],
        ])->validate();

        $bookingTime = Carbon::parse($data['booking_time'])->setSecond(0);
        $hour = (int) $bookingTime->format('G');

        if ($bookingTime->minute !== 0 || $hour < 9 || $hour > 16) {
            throw ValidationException::withMessages([
                'booking_time' => 'Ferry bookings must start on the hour between 9:00 and 16:00.',
            ]);
        }

        $ferry->loadMissing('island');

        if (
            $this->islandAccessService->ferryRequiresHotel($ferry)
            && ! $this->islandAccessService->hasConfirmedHotelStayAt($user, $bookingTime)
        ) {
            throw ValidationException::withMessages([
                'booking_time' => IslandAccessService::REQUIRED_STAY_ERROR,
            ]);
        }

        $bookedQuantity = FerryBooking::query()
            ->where('ferry_id', $ferry->id)
            ->where('booking_time', $bookingTime)
            ->where('status', '!=', 'canceled')
            ->sum('quantity');

        if ($bookedQuantity + $data['quantity'] > $ferry->max_capacity) {
            throw ValidationException::withMessages([
                'quantity' => 'Not enough capacity for that time slot.',
            ]);
        }

        $promotion = $this->promotionOfferService->resolveAppliedPromotion(
            $data['promotion_id'] ?? null,
            'ferry',
            $ferry,
        );
        $summary = [
            'type_label' => 'Ferry Transfer',
            'title' => $ferry->name,
            'subtitle' => $ferry->island?->name ?? 'Island transfer',
            'schedule_label' => $bookingTime->format('M d, Y \\a\\t H:i'),
            'quantity_label' => 'Tickets',
            'quantity' => (int) $data['quantity'],
            'unit_label' => 'Fare',
            'unit_price' => (float) $ferry->price,
            'total_price' => (float) ($ferry->price * $data['quantity']),
            'line_items' => [
                ['label' => 'Destination', 'value' => $ferry->island?->name ?? 'Island route'],
                ['label' => 'Departure', 'value' => $bookingTime->format('M d, Y \\a\\t H:i')],
                ['label' => 'Tickets', 'value' => (string) $data['quantity']],
                ['label' => 'Capacity', 'value' => (string) $ferry->max_capacity],
            ],
        ];

        $summary = $this->applyPromotionToSummary($summary, $promotion);

        return [
            'type' => 'ferry',
            'resource_id' => $ferry->id,
            'booking_data' => [
                'booking_time' => $bookingTime->format('Y-m-d H:i:s'),
                'quantity' => (int) $data['quantity'],
                'promotion_id' => $promotion['id'] ?? null,
            ],
            'summary' => $summary,
        ];
    }

    public function prepareRide(User $user, Ride $ride, array $input): array
    {
        $data = Validator::make($input, [
            'booking_time' => ['required', 'date'],
            'quantity' => ['required', 'integer', 'min:1', 'max:'.$ride->max_booking_quantity],
            'promotion_id' => ['nullable', 'integer', 'exists:promotions,id'],
        ])->validate();

        $bookingTime = Carbon::parse($data['booking_time'])->setSecond(0);
        $hour = (int) $bookingTime->format('G');

        if ($bookingTime->minute !== 0 || ! in_array($hour, [9, 17], true)) {
            throw ValidationException::withMessages([
                'booking_time' => 'Ride bookings are only available at 9:00 or 17:00.',
            ]);
        }

        $ride->loadMissing('island');

        if (
            $this->islandAccessService->rideRequiresHotel($ride)
            && ! $this->islandAccessService->hasConfirmedHotelStayAt($user, $bookingTime)
        ) {
            throw ValidationException::withMessages([
                'booking_time' => IslandAccessService::REQUIRED_STAY_ERROR,
            ]);
        }

        $bookedQuantity = RideBooking::query()
            ->where('ride_id', $ride->id)
            ->where('booking_time', $bookingTime)
            ->where('status', '!=', 'canceled')
            ->sum('quantity');

        if ($bookedQuantity + $data['quantity'] > $ride->max_capacity) {
            throw ValidationException::withMessages([
                'quantity' => 'Not enough capacity for that time slot.',
            ]);
        }

        $promotion = $this->promotionOfferService->resolveAppliedPromotion(
            $data['promotion_id'] ?? null,
            'ride',
            $ride,
        );
        $summary = [
            'type_label' => 'Ride Booking',
            'title' => $ride->name,
            'subtitle' => $ride->island?->name ?? 'Theme park ride',
            'schedule_label' => $bookingTime->format('M d, Y \\a\\t H:i'),
            'quantity_label' => 'Tickets',
            'quantity' => (int) $data['quantity'],
            'unit_label' => 'Ticket',
            'unit_price' => (float) $ride->price,
            'total_price' => (float) ($ride->price * $data['quantity']),
            'line_items' => [
                ['label' => 'Island', 'value' => $ride->island?->name ?? 'Horror Island'],
                ['label' => 'Session', 'value' => $bookingTime->format('M d, Y \\a\\t H:i')],
                ['label' => 'Tickets', 'value' => (string) $data['quantity']],
                ['label' => 'Capacity', 'value' => (string) $ride->max_capacity],
            ],
        ];

        $summary = $this->applyPromotionToSummary($summary, $promotion);

        return [
            'type' => 'ride',
            'resource_id' => $ride->id,
            'booking_data' => [
                'booking_time' => $bookingTime->format('Y-m-d H:i:s'),
                'quantity' => (int) $data['quantity'],
                'promotion_id' => $promotion['id'] ?? null,
            ],
            'summary' => $summary,
        ];
    }

    public function prepareGame(User $user, Game $game, array $input): array
    {
        $data = Validator::make($input, [
            'booking_time' => ['required', 'date'],
            'quantity' => ['required', 'integer', 'min:1', 'max:'.$game->max_booking_quantity],
            'promotion_id' => ['nullable', 'integer', 'exists:promotions,id'],
        ])->validate();

        $bookingTime = Carbon::parse($data['booking_time'])->setSecond(0);
        $hour = (int) $bookingTime->format('G');

        if ($bookingTime->minute !== 0 || ! in_array($hour, [9, 17], true)) {
            throw ValidationException::withMessages([
                'booking_time' => 'Game bookings are only available at 9:00 or 17:00.',
            ]);
        }

        $game->loadMissing('island');

        if (
            $this->islandAccessService->gameRequiresHotel($game)
            && ! $this->islandAccessService->hasConfirmedHotelStayAt($user, $bookingTime)
        ) {
            throw ValidationException::withMessages([
                'booking_time' => IslandAccessService::REQUIRED_STAY_ERROR,
            ]);
        }

        $bookedQuantity = GameBooking::query()
            ->where('game_id', $game->id)
            ->where('booking_time', $bookingTime)
            ->where('status', '!=', 'canceled')
            ->sum('quantity');

        if ($bookedQuantity + $data['quantity'] > $game->max_capacity) {
            throw ValidationException::withMessages([
                'quantity' => 'Not enough capacity for that time slot.',
            ]);
        }

        $promotion = $this->promotionOfferService->resolveAppliedPromotion(
            $data['promotion_id'] ?? null,
            'game',
            $game,
        );
        $summary = [
            'type_label' => 'Game Booking',
            'title' => $game->name,
            'subtitle' => $game->island?->name ?? 'Theme park game',
            'schedule_label' => $bookingTime->format('M d, Y \\a\\t H:i'),
            'quantity_label' => 'Players',
            'quantity' => (int) $data['quantity'],
            'unit_label' => 'Player ticket',
            'unit_price' => (float) $game->price,
            'total_price' => (float) ($game->price * $data['quantity']),
            'line_items' => [
                ['label' => 'Island', 'value' => $game->island?->name ?? 'Horror Island'],
                ['label' => 'Session', 'value' => $bookingTime->format('M d, Y \\a\\t H:i')],
                ['label' => 'Players', 'value' => (string) $data['quantity']],
                ['label' => 'Capacity', 'value' => (string) $game->max_capacity],
            ],
        ];

        $summary = $this->applyPromotionToSummary($summary, $promotion);

        return [
            'type' => 'game',
            'resource_id' => $game->id,
            'booking_data' => [
                'booking_time' => $bookingTime->format('Y-m-d H:i:s'),
                'quantity' => (int) $data['quantity'],
                'promotion_id' => $promotion['id'] ?? null,
            ],
            'summary' => $summary,
        ];
    }

    public function prepareBeachEvent(User $user, BeachEvent $beachEvent, array $input): array
    {
        $data = Validator::make($input, [
            'booking_date' => ['required', 'date'],
            'booking_time' => ['required', 'date_format:H:i'],
            'quantity' => ['required', 'integer', 'min:1', 'max:'.$beachEvent->max_booking_quantity],
            'promotion_id' => ['nullable', 'integer', 'exists:promotions,id'],
        ])->validate();

        $bookingDate = Carbon::parse($data['booking_date'])->toDateString();
        $eventDate = Carbon::parse($beachEvent->event_date)->toDateString();

        if ($bookingDate !== $eventDate) {
            throw ValidationException::withMessages([
                'booking_date' => 'Booking date must match the event date.',
            ]);
        }

        $bookingTime = Carbon::parse($bookingDate.' '.$data['booking_time'])->setSecond(0);

        $beachEvent->loadMissing('island', 'owner');

        if (
            $this->islandAccessService->beachEventRequiresHotel($beachEvent)
            && ! $this->islandAccessService->hasConfirmedHotelStayAt($user, $bookingTime)
        ) {
            throw ValidationException::withMessages([
                'booking_time' => IslandAccessService::REQUIRED_STAY_ERROR,
            ]);
        }

        $bookedQuantity = BeachEventBooking::query()
            ->where('beach_event_id', $beachEvent->id)
            ->where('booking_date', $bookingDate)
            ->where('booking_time', $bookingTime)
            ->where('status', '!=', 'canceled')
            ->sum('quantity');

        if ($bookedQuantity + $data['quantity'] > $beachEvent->max_capacity) {
            throw ValidationException::withMessages([
                'quantity' => 'Not enough capacity for that time slot.',
            ]);
        }

        $promotion = $this->promotionOfferService->resolveAppliedPromotion(
            $data['promotion_id'] ?? null,
            'beach-event',
            $beachEvent,
        );
        $summary = [
            'type_label' => 'Beach Event',
            'title' => $beachEvent->name,
            'subtitle' => $beachEvent->island?->name ?? 'Beach event',
            'schedule_label' => $bookingTime->format('M d, Y \\a\\t H:i'),
            'quantity_label' => 'Tickets',
            'quantity' => (int) $data['quantity'],
            'unit_label' => 'Entry ticket',
            'unit_price' => (float) $beachEvent->price,
            'total_price' => (float) ($beachEvent->price * $data['quantity']),
            'line_items' => [
                ['label' => 'Organizer', 'value' => $beachEvent->owner?->name ?? 'Event host'],
                ['label' => 'Island', 'value' => $beachEvent->island?->name ?? 'Picnic Island'],
                ['label' => 'Session', 'value' => $bookingTime->format('M d, Y \\a\\t H:i')],
                ['label' => 'Tickets', 'value' => (string) $data['quantity']],
            ],
        ];

        $summary = $this->applyPromotionToSummary($summary, $promotion);

        return [
            'type' => 'beach-event',
            'resource_id' => $beachEvent->id,
            'booking_data' => [
                'booking_date' => $bookingDate,
                'booking_time' => $bookingTime->format('H:i'),
                'quantity' => (int) $data['quantity'],
                'promotion_id' => $promotion['id'] ?? null,
            ],
            'summary' => $summary,
        ];
    }

    public function createHotel(User $user, Room $room, array $input): HotelBooking
    {
        $checkout = $this->prepareHotel($user, $room, $input);
        $data = $checkout['booking_data'];

        $booking = HotelBooking::create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'start_date' => Carbon::parse($data['start_date'])->startOfDay(),
            'end_date' => Carbon::parse($data['end_date'])->startOfDay(),
            'quantity' => $data['quantity'],
            'total_price' => $checkout['summary']['total_price'],
            'status' => 'confirmed',
        ]);

        $this->bookingLifecycleService->createConfirmedBooking($booking, $user);

        return $booking->fresh();
    }

    public function createFerry(User $user, Ferry $ferry, array $input): FerryBooking
    {
        $checkout = $this->prepareFerry($user, $ferry, $input);
        $data = $checkout['booking_data'];

        $booking = FerryBooking::create([
            'user_id' => $user->id,
            'ferry_id' => $ferry->id,
            'booking_time' => Carbon::parse($data['booking_time'])->setSecond(0),
            'quantity' => $data['quantity'],
            'total_price' => $checkout['summary']['total_price'],
            'status' => 'confirmed',
        ]);

        $this->bookingLifecycleService->createConfirmedBooking($booking, $user);

        return $booking->fresh();
    }

    public function createRide(User $user, Ride $ride, array $input): RideBooking
    {
        $checkout = $this->prepareRide($user, $ride, $input);
        $data = $checkout['booking_data'];

        $booking = RideBooking::create([
            'user_id' => $user->id,
            'ride_id' => $ride->id,
            'booking_time' => Carbon::parse($data['booking_time'])->setSecond(0),
            'quantity' => $data['quantity'],
            'total_price' => $checkout['summary']['total_price'],
            'status' => 'confirmed',
        ]);

        $this->bookingLifecycleService->createConfirmedBooking($booking, $user);

        return $booking->fresh();
    }

    public function createGame(User $user, Game $game, array $input): GameBooking
    {
        $checkout = $this->prepareGame($user, $game, $input);
        $data = $checkout['booking_data'];

        $booking = GameBooking::create([
            'user_id' => $user->id,
            'game_id' => $game->id,
            'booking_time' => Carbon::parse($data['booking_time'])->setSecond(0),
            'quantity' => $data['quantity'],
            'total_price' => $checkout['summary']['total_price'],
            'status' => 'confirmed',
        ]);

        $this->bookingLifecycleService->createConfirmedBooking($booking, $user);

        return $booking->fresh();
    }

    public function createBeachEvent(User $user, BeachEvent $beachEvent, array $input): BeachEventBooking
    {
        $checkout = $this->prepareBeachEvent($user, $beachEvent, $input);
        $data = $checkout['booking_data'];

        $bookingDate = Carbon::parse($data['booking_date'])->toDateString();
        $bookingTime = Carbon::parse($bookingDate.' '.$data['booking_time'])->setSecond(0);

        $booking = BeachEventBooking::create([
            'user_id' => $user->id,
            'beach_event_id' => $beachEvent->id,
            'booking_date' => $bookingDate,
            'booking_time' => $bookingTime,
            'quantity' => $data['quantity'],
            'total_price' => $checkout['summary']['total_price'],
            'status' => 'confirmed',
        ]);

        $this->bookingLifecycleService->createConfirmedBooking($booking, $user);

        return $booking->fresh();
    }

    public function createFromCheckout(User $user, array $checkout): Model
    {
        return match ($checkout['type']) {
            'hotel' => $this->createHotel($user, Room::query()->findOrFail($checkout['resource_id']), $checkout['booking_data']),
            'ferry' => $this->createFerry($user, Ferry::query()->findOrFail($checkout['resource_id']), $checkout['booking_data']),
            'ride' => $this->createRide($user, Ride::query()->findOrFail($checkout['resource_id']), $checkout['booking_data']),
            'game' => $this->createGame($user, Game::query()->findOrFail($checkout['resource_id']), $checkout['booking_data']),
            'beach-event' => $this->createBeachEvent($user, BeachEvent::query()->findOrFail($checkout['resource_id']), $checkout['booking_data']),
            default => throw ValidationException::withMessages([
                'checkout' => 'Unsupported booking checkout type.',
            ]),
        };
    }

    private function applyPromotionToSummary(array $summary, ?array $promotion): array
    {
        if (! $promotion || ($promotion['discount_percentage'] ?? 0) <= 0) {
            $summary['base_unit_price'] = (float) $summary['unit_price'];
            $summary['base_total_price'] = (float) $summary['total_price'];
            $summary['discount_amount'] = 0.0;
            $summary['promotion'] = null;

            return $summary;
        }

        $discountPercentage = (float) $promotion['discount_percentage'];
        $baseUnitPrice = (float) $summary['unit_price'];
        $baseTotalPrice = (float) $summary['total_price'];
        $discountedUnitPrice = $this->discountPrice($baseUnitPrice, $discountPercentage);
        $discountedTotalPrice = $this->discountPrice($baseTotalPrice, $discountPercentage);

        $summary['base_unit_price'] = $baseUnitPrice;
        $summary['base_total_price'] = $baseTotalPrice;
        $summary['unit_price'] = $discountedUnitPrice;
        $summary['total_price'] = $discountedTotalPrice;
        $summary['discount_amount'] = round($baseTotalPrice - $discountedTotalPrice, 2);
        $summary['promotion'] = [
            'id' => $promotion['id'],
            'title' => $promotion['title'],
            'label' => $promotion['label'],
        ];
        $summary['line_items'][] = [
            'label' => 'Offer',
            'value' => $promotion['title'].' · '.$promotion['label'],
        ];

        return $summary;
    }

    private function discountPrice(float $price, float $discountPercentage): float
    {
        if ($discountPercentage <= 0) {
            return round($price, 2);
        }

        return round($price * (1 - ($discountPercentage / 100)), 2);
    }
}
