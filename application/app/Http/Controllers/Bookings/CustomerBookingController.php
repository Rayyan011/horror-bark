<?php

namespace App\Http\Controllers\Bookings;

use App\Http\Controllers\Controller;
use App\Models\BeachEventBooking;
use App\Models\FerryBooking;
use App\Models\GameBooking;
use App\Models\HotelBooking;
use App\Models\RideBooking;
use App\Notifications\BookingCanceledNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerBookingController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $filters = $request->validate([
            'type' => ['nullable', Rule::in(['hotel', 'ferry', 'ride', 'game', 'beach-event'])],
            'search' => ['nullable', 'string', 'max:120'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
            'receipt_search' => ['nullable', 'string', 'max:120'],
            'receipt_status' => ['nullable', Rule::in(['issued', 'canceled'])],
        ]);

        $bookings = collect();
        $selectedType = $filters['type'] ?? null;

        if (!$selectedType || $selectedType === 'hotel') {
            $bookings = $bookings->merge(
                $this->buildHotelQuery($user, $filters)->get()->map(
                    fn (HotelBooking $booking) => $this->mapHotelBooking($booking)
                )
            );
        }

        if (!$selectedType || $selectedType === 'ferry') {
            $bookings = $bookings->merge(
                $this->buildFerryQuery($user, $filters)->get()->map(
                    fn (FerryBooking $booking) => $this->mapFerryBooking($booking)
                )
            );
        }

        if (!$selectedType || $selectedType === 'ride') {
            $bookings = $bookings->merge(
                $this->buildRideQuery($user, $filters)->get()->map(
                    fn (RideBooking $booking) => $this->mapRideBooking($booking)
                )
            );
        }

        if (!$selectedType || $selectedType === 'game') {
            $bookings = $bookings->merge(
                $this->buildGameQuery($user, $filters)->get()->map(
                    fn (GameBooking $booking) => $this->mapGameBooking($booking)
                )
            );
        }

        if (!$selectedType || $selectedType === 'beach-event') {
            $bookings = $bookings->merge(
                $this->buildBeachEventQuery($user, $filters)->get()->map(
                    fn (BeachEventBooking $booking) => $this->mapBeachEventBooking($booking)
                )
            );
        }

        $bookings = $bookings
            ->sortByDesc(fn (array $item) => $item['sort_at']->timestamp)
            ->values();

        $bookingGroups = [
            'pending' => $bookings->where('status', 'pending')->values(),
            'confirmed' => $bookings->where('status', 'confirmed')->values(),
            'canceled' => $bookings->where('status', 'canceled')->values(),
        ];

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

        $receiptsQuery = $user->invoices()->latest('issued_at');

        if (!empty($filters['receipt_search'])) {
            $search = trim($filters['receipt_search']);
            $receiptsQuery->where('invoice_number', 'like', '%' . $search . '%');
        }

        if (!empty($filters['receipt_status'])) {
            $receiptsQuery->where('status', $filters['receipt_status']);
        }

        $receipts = $receiptsQuery->paginate(10, ['*'], 'receipt_page')->withQueryString();

        return view('pages.bookings.index', compact(
            'bookingGroups',
            'filters',
            'stats',
            'receipts'
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
            $hotelBooking->user->notify(new BookingCanceledNotification($hotelBooking, 'Hotel'));
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
            $ferryBooking->user->notify(new BookingCanceledNotification($ferryBooking, 'Ferry'));
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
            $rideBooking->user->notify(new BookingCanceledNotification($rideBooking, 'Ride'));
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
            $gameBooking->user->notify(new BookingCanceledNotification($gameBooking, 'Game'));
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
            $beachEventBooking->user->notify(new BookingCanceledNotification($beachEventBooking, 'Beach Event'));
        }

        return back()->with('status', 'Beach event booking canceled.');
    }

    private function buildHotelQuery($user, array $filters)
    {
        $query = $user->hotelBookings()->with('room.hotel')->latest();

        if (!empty($filters['search'])) {
            $query->where(function ($builder) use ($filters) {
                $builder->whereHas('room.hotel', function ($inner) use ($filters) {
                    $inner->where('name', 'like', '%' . $filters['search'] . '%');
                })->orWhereHas('room', function ($inner) use ($filters) {
                    $inner->where('room_number', 'like', '%' . $filters['search'] . '%');
                });
            });
        }

        if (!empty($filters['from'])) {
            $query->whereDate('start_date', '>=', $filters['from']);
        }

        if (!empty($filters['to'])) {
            $query->whereDate('start_date', '<=', $filters['to']);
        }

        return $query;
    }

    private function buildFerryQuery($user, array $filters)
    {
        $query = $user->ferryBookings()->with('ferry')->latest();

        if (!empty($filters['search'])) {
            $query->whereHas('ferry', function ($builder) use ($filters) {
                $builder->where('name', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['from'])) {
            $query->whereDate('booking_time', '>=', $filters['from']);
        }

        if (!empty($filters['to'])) {
            $query->whereDate('booking_time', '<=', $filters['to']);
        }

        return $query;
    }

    private function buildRideQuery($user, array $filters)
    {
        $query = $user->rideBookings()->with('ride')->latest();

        if (!empty($filters['search'])) {
            $query->whereHas('ride', function ($builder) use ($filters) {
                $builder->where('name', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['from'])) {
            $query->whereDate('booking_time', '>=', $filters['from']);
        }

        if (!empty($filters['to'])) {
            $query->whereDate('booking_time', '<=', $filters['to']);
        }

        return $query;
    }

    private function buildGameQuery($user, array $filters)
    {
        $query = $user->gameBookings()->with('game')->latest();

        if (!empty($filters['search'])) {
            $query->whereHas('game', function ($builder) use ($filters) {
                $builder->where('name', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['from'])) {
            $query->whereDate('booking_time', '>=', $filters['from']);
        }

        if (!empty($filters['to'])) {
            $query->whereDate('booking_time', '<=', $filters['to']);
        }

        return $query;
    }

    private function buildBeachEventQuery($user, array $filters)
    {
        $query = $user->beachEventBookings()->with('beachEvent')->latest();

        if (!empty($filters['search'])) {
            $query->whereHas('beachEvent', function ($builder) use ($filters) {
                $builder->where('name', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['from'])) {
            $query->whereDate('booking_date', '>=', $filters['from']);
        }

        if (!empty($filters['to'])) {
            $query->whereDate('booking_date', '<=', $filters['to']);
        }

        return $query;
    }

    private function mapHotelBooking(HotelBooking $booking): array
    {
        $sortAt = Carbon::parse($booking->start_date);

        return [
            'id' => $booking->id,
            'type' => 'hotel',
            'type_label' => 'Hotel',
            'status' => $booking->status,
            'title' => $booking->room->hotel->name ?? 'Hotel',
            'subtitle' => 'Room ' . ($booking->room->room_number ?? 'N/A'),
            'schedule' => Carbon::parse($booking->start_date)->toDateString() . ' → ' . Carbon::parse($booking->end_date)->toDateString(),
            'quantity' => $booking->quantity,
            'total_price' => (float) $booking->total_price,
            'detail_url' => route('bookings.hotels.show', $booking),
            'cancel_url' => route('bookings.hotels.cancel', $booking),
            'can_cancel' => $booking->status !== 'canceled',
            'sort_at' => $sortAt,
        ];
    }

    private function mapFerryBooking(FerryBooking $booking): array
    {
        $sortAt = Carbon::parse($booking->booking_time);

        return [
            'id' => $booking->id,
            'type' => 'ferry',
            'type_label' => 'Ferry',
            'status' => $booking->status,
            'title' => $booking->ferry->name ?? 'Ferry',
            'subtitle' => 'Ferry ticket',
            'schedule' => $sortAt->format('Y-m-d H:i'),
            'quantity' => $booking->quantity,
            'total_price' => (float) $booking->total_price,
            'detail_url' => route('bookings.ferries.show', $booking),
            'cancel_url' => route('bookings.ferries.cancel', $booking),
            'can_cancel' => $booking->status !== 'canceled',
            'sort_at' => $sortAt,
        ];
    }

    private function mapRideBooking(RideBooking $booking): array
    {
        $sortAt = Carbon::parse($booking->booking_time);

        return [
            'id' => $booking->id,
            'type' => 'ride',
            'type_label' => 'Ride',
            'status' => $booking->status,
            'title' => $booking->ride->name ?? 'Ride',
            'subtitle' => 'Ride ticket',
            'schedule' => $sortAt->format('Y-m-d H:i'),
            'quantity' => $booking->quantity,
            'total_price' => (float) $booking->total_price,
            'detail_url' => route('bookings.rides.show', $booking),
            'cancel_url' => route('bookings.rides.cancel', $booking),
            'can_cancel' => $booking->status !== 'canceled',
            'sort_at' => $sortAt,
        ];
    }

    private function mapGameBooking(GameBooking $booking): array
    {
        $sortAt = Carbon::parse($booking->booking_time);

        return [
            'id' => $booking->id,
            'type' => 'game',
            'type_label' => 'Game',
            'status' => $booking->status,
            'title' => $booking->game->name ?? 'Game',
            'subtitle' => 'Game pass',
            'schedule' => $sortAt->format('Y-m-d H:i'),
            'quantity' => $booking->quantity,
            'total_price' => (float) $booking->total_price,
            'detail_url' => route('bookings.games.show', $booking),
            'cancel_url' => route('bookings.games.cancel', $booking),
            'can_cancel' => $booking->status !== 'canceled',
            'sort_at' => $sortAt,
        ];
    }

    private function mapBeachEventBooking(BeachEventBooking $booking): array
    {
        $sortAt = Carbon::parse($booking->booking_time);

        return [
            'id' => $booking->id,
            'type' => 'beach-event',
            'type_label' => 'Beach Event',
            'status' => $booking->status,
            'title' => $booking->beachEvent->name ?? 'Beach Event',
            'subtitle' => 'Event ticket',
            'schedule' => Carbon::parse($booking->booking_date)->toDateString() . ' ' . $sortAt->format('H:i'),
            'quantity' => $booking->quantity,
            'total_price' => (float) $booking->total_price,
            'detail_url' => route('bookings.beach-events.show', $booking),
            'cancel_url' => route('bookings.beach-events.cancel', $booking),
            'can_cancel' => $booking->status !== 'canceled',
            'sort_at' => $sortAt,
        ];
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
