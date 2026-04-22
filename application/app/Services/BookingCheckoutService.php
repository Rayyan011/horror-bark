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
    ) {}

    public function prepareHotel(User $user, Room $room, array $input): array
    {
        $data = Validator::make($input, [
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'quantity' => ['required', 'integer', 'min:1', 'max:'.$room->max_occupancy],
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

        return [
            'type' => 'hotel',
            'resource_id' => $room->id,
            'booking_data' => [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'quantity' => (int) $data['quantity'],
            ],
            'summary' => [
                'type_label' => 'Hotel Stay',
                'title' => $room->hotel?->name ?? 'Hotel stay',
                'subtitle' => 'Room '.$room->room_number,
                'schedule_label' => $startDate->format('M d, Y').' to '.$endDate->format('M d, Y'),
                'quantity_label' => 'Guests',
                'quantity' => (int) $data['quantity'],
                'unit_label' => 'Nightly rate',
                'unit_price' => (float) $room->price,
                'total_price' => (float) ($room->price * $data['quantity'] * $nights),
                'line_items' => [
                    ['label' => 'Guests', 'value' => (string) $data['quantity']],
                    ['label' => 'Stay', 'value' => $nights.' night'.($nights === 1 ? '' : 's')],
                    ['label' => 'Check-in', 'value' => $startDate->format('M d, Y')],
                    ['label' => 'Check-out', 'value' => $endDate->format('M d, Y')],
                ],
            ],
        ];
    }

    public function prepareFerry(User $user, Ferry $ferry, array $input): array
    {
        $data = Validator::make($input, [
            'booking_time' => ['required', 'date'],
            'quantity' => ['required', 'integer', 'min:1', 'max:'.$ferry->max_booking_quantity],
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

        return [
            'type' => 'ferry',
            'resource_id' => $ferry->id,
            'booking_data' => [
                'booking_time' => $bookingTime->format('Y-m-d H:i:s'),
                'quantity' => (int) $data['quantity'],
            ],
            'summary' => [
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
            ],
        ];
    }

    public function prepareRide(User $user, Ride $ride, array $input): array
    {
        $data = Validator::make($input, [
            'booking_time' => ['required', 'date'],
            'quantity' => ['required', 'integer', 'min:1', 'max:'.$ride->max_booking_quantity],
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

        return [
            'type' => 'ride',
            'resource_id' => $ride->id,
            'booking_data' => [
                'booking_time' => $bookingTime->format('Y-m-d H:i:s'),
                'quantity' => (int) $data['quantity'],
            ],
            'summary' => [
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
                    ['label' => 'District', 'value' => $ride->island?->name ?? 'Horror Island'],
                    ['label' => 'Session', 'value' => $bookingTime->format('M d, Y \\a\\t H:i')],
                    ['label' => 'Tickets', 'value' => (string) $data['quantity']],
                    ['label' => 'Capacity', 'value' => (string) $ride->max_capacity],
                ],
            ],
        ];
    }

    public function prepareGame(User $user, Game $game, array $input): array
    {
        $data = Validator::make($input, [
            'booking_time' => ['required', 'date'],
            'quantity' => ['required', 'integer', 'min:1', 'max:'.$game->max_booking_quantity],
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

        return [
            'type' => 'game',
            'resource_id' => $game->id,
            'booking_data' => [
                'booking_time' => $bookingTime->format('Y-m-d H:i:s'),
                'quantity' => (int) $data['quantity'],
            ],
            'summary' => [
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
                    ['label' => 'District', 'value' => $game->island?->name ?? 'Horror Island'],
                    ['label' => 'Session', 'value' => $bookingTime->format('M d, Y \\a\\t H:i')],
                    ['label' => 'Players', 'value' => (string) $data['quantity']],
                    ['label' => 'Capacity', 'value' => (string) $game->max_capacity],
                ],
            ],
        ];
    }

    public function prepareBeachEvent(User $user, BeachEvent $beachEvent, array $input): array
    {
        $data = Validator::make($input, [
            'booking_date' => ['required', 'date'],
            'booking_time' => ['required', 'date_format:H:i'],
            'quantity' => ['required', 'integer', 'min:1', 'max:'.$beachEvent->max_booking_quantity],
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

        return [
            'type' => 'beach-event',
            'resource_id' => $beachEvent->id,
            'booking_data' => [
                'booking_date' => $bookingDate,
                'booking_time' => $bookingTime->format('H:i'),
                'quantity' => (int) $data['quantity'],
            ],
            'summary' => [
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
                    ['label' => 'Shore', 'value' => $beachEvent->island?->name ?? 'Picnic Island'],
                    ['label' => 'Session', 'value' => $bookingTime->format('M d, Y \\a\\t H:i')],
                    ['label' => 'Tickets', 'value' => (string) $data['quantity']],
                ],
            ],
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
}
