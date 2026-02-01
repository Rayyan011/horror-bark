<?php

namespace App\Http\Controllers\Bookings;

use App\Http\Controllers\Controller;
use App\Models\BeachEventBooking;
use App\Models\FerryBooking;
use App\Models\GameBooking;
use App\Models\HotelBooking;
use App\Models\RideBooking;
use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CustomerBookingController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $filters = [
            'type' => $request->query('type'),
            'status' => $request->query('status'),
            'search' => $request->query('search'),
            'from' => $request->query('from'),
            'to' => $request->query('to'),
        ];

        $hotelBookings = $this->buildHotelQuery($user, $filters)
            ->paginate(10, ['*'], 'hotel_page');

        $ferryBookings = $this->buildFerryQuery($user, $filters)
            ->paginate(10, ['*'], 'ferry_page');

        $rideBookings = $this->buildRideQuery($user, $filters)
            ->paginate(10, ['*'], 'ride_page');

        $gameBookings = $this->buildGameQuery($user, $filters)
            ->paginate(10, ['*'], 'game_page');

        $beachEventBookings = $this->buildBeachEventQuery($user, $filters)
            ->paginate(10, ['*'], 'beach_event_page');

        if ($filters['type']) {
            $allowed = [
                'hotel' => 'hotelBookings',
                'ferry' => 'ferryBookings',
                'ride' => 'rideBookings',
                'game' => 'gameBookings',
                'beach-event' => 'beachEventBookings',
            ];
            foreach ($allowed as $type => $property) {
                if ($filters['type'] !== $type) {
                    $$property = $this->emptyPaginator();
                }
            }
        }

        $stats = [
            'total' => $user->hotelBookings()->count()
                + $user->ferryBookings()->count()
                + $user->rideBookings()->count()
                + $user->gameBookings()->count()
                + $user->beachEventBookings()->count(),
            'upcoming' => $this->countUpcoming($user),
            'spent' => (float) ($user->hotelBookings()->sum('total_price')
                + $user->ferryBookings()->sum('total_price')
                + $user->rideBookings()->sum('total_price')
                + $user->gameBookings()->sum('total_price')
                + $user->beachEventBookings()->sum('total_price')),
        ];

        return view('pages.bookings.index', compact(
            'hotelBookings',
            'ferryBookings',
            'rideBookings',
            'gameBookings',
            'beachEventBookings',
            'filters',
            'stats'
        ));
    }

    public function showHotel(HotelBooking $hotelBooking)
    {
        $this->authorize('view', $hotelBooking);

        return view('pages.bookings.show', [
            'booking' => $hotelBooking->load('room.hotel', 'invoice'),
            'type' => 'Hotel',
            'invoice' => $hotelBooking->invoice,
            'cancelRoute' => route('bookings.hotels.cancel', $hotelBooking),
        ]);
    }

    public function showFerry(FerryBooking $ferryBooking)
    {
        $this->authorize('view', $ferryBooking);

        return view('pages.bookings.show', [
            'booking' => $ferryBooking->load('ferry', 'invoice'),
            'type' => 'Ferry',
            'invoice' => $ferryBooking->invoice,
            'cancelRoute' => route('bookings.ferries.cancel', $ferryBooking),
        ]);
    }

    public function showRide(RideBooking $rideBooking)
    {
        $this->authorize('view', $rideBooking);

        return view('pages.bookings.show', [
            'booking' => $rideBooking->load('ride', 'invoice'),
            'type' => 'Ride',
            'invoice' => $rideBooking->invoice,
            'cancelRoute' => route('bookings.rides.cancel', $rideBooking),
        ]);
    }

    public function showGame(GameBooking $gameBooking)
    {
        $this->authorize('view', $gameBooking);

        return view('pages.bookings.show', [
            'booking' => $gameBooking->load('game', 'invoice'),
            'type' => 'Game',
            'invoice' => $gameBooking->invoice,
            'cancelRoute' => route('bookings.games.cancel', $gameBooking),
        ]);
    }

    public function showBeachEvent(BeachEventBooking $beachEventBooking)
    {
        $this->authorize('view', $beachEventBooking);

        return view('pages.bookings.show', [
            'booking' => $beachEventBooking->load('beachEvent', 'invoice'),
            'type' => 'Beach Event',
            'invoice' => $beachEventBooking->invoice,
            'cancelRoute' => route('bookings.beach-events.cancel', $beachEventBooking),
        ]);
    }

    public function cancelHotel(HotelBooking $hotelBooking)
    {
        $this->authorize('update', $hotelBooking);

        if ($hotelBooking->status !== 'canceled') {
            $hotelBooking->update(['status' => 'canceled']);
            if ($hotelBooking->invoice) {
                $hotelBooking->invoice->update(['status' => 'canceled']);
            }
        }

        return back()->with('status', 'Hotel booking canceled.');
    }

    public function cancelFerry(FerryBooking $ferryBooking)
    {
        $this->authorize('update', $ferryBooking);

        if ($ferryBooking->status !== 'canceled') {
            $ferryBooking->update(['status' => 'canceled']);
            if ($ferryBooking->invoice) {
                $ferryBooking->invoice->update(['status' => 'canceled']);
            }
        }

        return back()->with('status', 'Ferry booking canceled.');
    }

    public function cancelRide(RideBooking $rideBooking)
    {
        $this->authorize('update', $rideBooking);

        if ($rideBooking->status !== 'canceled') {
            $rideBooking->update(['status' => 'canceled']);
            if ($rideBooking->invoice) {
                $rideBooking->invoice->update(['status' => 'canceled']);
            }
        }

        return back()->with('status', 'Ride booking canceled.');
    }

    public function cancelGame(GameBooking $gameBooking)
    {
        $this->authorize('update', $gameBooking);

        if ($gameBooking->status !== 'canceled') {
            $gameBooking->update(['status' => 'canceled']);
            if ($gameBooking->invoice) {
                $gameBooking->invoice->update(['status' => 'canceled']);
            }
        }

        return back()->with('status', 'Game booking canceled.');
    }

    public function cancelBeachEvent(BeachEventBooking $beachEventBooking)
    {
        $this->authorize('update', $beachEventBooking);

        if ($beachEventBooking->status !== 'canceled') {
            $beachEventBooking->update(['status' => 'canceled']);
            if ($beachEventBooking->invoice) {
                $beachEventBooking->invoice->update(['status' => 'canceled']);
            }
        }

        return back()->with('status', 'Beach event booking canceled.');
    }

    private function buildHotelQuery($user, array $filters)
    {
        $query = $user->hotelBookings()->with('room.hotel')->latest();

        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }

        if ($filters['search']) {
            $query->where(function ($builder) use ($filters) {
                $builder->whereHas('room.hotel', function ($inner) use ($filters) {
                    $inner->where('name', 'like', '%' . $filters['search'] . '%');
                })->orWhereHas('room', function ($inner) use ($filters) {
                    $inner->where('room_number', 'like', '%' . $filters['search'] . '%');
                });
            });
        }

        if ($filters['from']) {
            $query->where('start_date', '>=', $filters['from']);
        }

        if ($filters['to']) {
            $query->where('start_date', '<=', $filters['to']);
        }

        return $query;
    }

    private function buildFerryQuery($user, array $filters)
    {
        $query = $user->ferryBookings()->with('ferry')->latest();

        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }

        if ($filters['search']) {
            $query->whereHas('ferry', function ($builder) use ($filters) {
                $builder->where('name', 'like', '%' . $filters['search'] . '%');
            });
        }

        if ($filters['from']) {
            $query->where('booking_time', '>=', $filters['from']);
        }

        if ($filters['to']) {
            $query->where('booking_time', '<=', $filters['to']);
        }

        return $query;
    }

    private function buildRideQuery($user, array $filters)
    {
        $query = $user->rideBookings()->with('ride')->latest();

        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }

        if ($filters['search']) {
            $query->whereHas('ride', function ($builder) use ($filters) {
                $builder->where('name', 'like', '%' . $filters['search'] . '%');
            });
        }

        if ($filters['from']) {
            $query->where('booking_time', '>=', $filters['from']);
        }

        if ($filters['to']) {
            $query->where('booking_time', '<=', $filters['to']);
        }

        return $query;
    }

    private function buildGameQuery($user, array $filters)
    {
        $query = $user->gameBookings()->with('game')->latest();

        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }

        if ($filters['search']) {
            $query->whereHas('game', function ($builder) use ($filters) {
                $builder->where('name', 'like', '%' . $filters['search'] . '%');
            });
        }

        if ($filters['from']) {
            $query->where('booking_time', '>=', $filters['from']);
        }

        if ($filters['to']) {
            $query->where('booking_time', '<=', $filters['to']);
        }

        return $query;
    }

    private function buildBeachEventQuery($user, array $filters)
    {
        $query = $user->beachEventBookings()->with('beachEvent')->latest();

        if ($filters['status']) {
            $query->where('status', $filters['status']);
        }

        if ($filters['search']) {
            $query->whereHas('beachEvent', function ($builder) use ($filters) {
                $builder->where('name', 'like', '%' . $filters['search'] . '%');
            });
        }

        if ($filters['from']) {
            $query->where('booking_date', '>=', $filters['from']);
        }

        if ($filters['to']) {
            $query->where('booking_date', '<=', $filters['to']);
        }

        return $query;
    }

    private function emptyPaginator(): LengthAwarePaginator
    {
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        $paginator->setPath(url()->current());

        return $paginator;
    }

    private function countUpcoming($user): int
    {
        $now = now();

        return $user->hotelBookings()->where('start_date', '>=', $now)->count()
            + $user->ferryBookings()->where('booking_time', '>=', $now)->count()
            + $user->rideBookings()->where('booking_time', '>=', $now)->count()
            + $user->gameBookings()->where('booking_time', '>=', $now)->count()
            + $user->beachEventBookings()->where('booking_date', '>=', $now->toDateString())->count();
    }
}
