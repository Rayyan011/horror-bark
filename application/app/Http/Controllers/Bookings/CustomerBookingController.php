<?php

namespace App\Http\Controllers\Bookings;

use App\Http\Controllers\Controller;
use App\Models\BeachEventBooking;
use App\Models\FerryBooking;
use App\Models\GameBooking;
use App\Models\HotelBooking;
use App\Models\RideBooking;
use App\Services\BookingLifecycleService;
use App\Support\BookingSupport;
use Illuminate\Database\Eloquent\Model;
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

        if (! $selectedType || $selectedType === 'hotel') {
            $bookings = $bookings->merge(
                $this->buildHotelQuery($user, $filters)->get()->map(
                    fn (HotelBooking $booking) => $this->mapBookingCard($booking)
                )
            );
        }

        if (! $selectedType || $selectedType === 'ferry') {
            $bookings = $bookings->merge(
                $this->buildFerryQuery($user, $filters)->get()->map(
                    fn (FerryBooking $booking) => $this->mapBookingCard($booking)
                )
            );
        }

        if (! $selectedType || $selectedType === 'ride') {
            $bookings = $bookings->merge(
                $this->buildRideQuery($user, $filters)->get()->map(
                    fn (RideBooking $booking) => $this->mapBookingCard($booking)
                )
            );
        }

        if (! $selectedType || $selectedType === 'game') {
            $bookings = $bookings->merge(
                $this->buildGameQuery($user, $filters)->get()->map(
                    fn (GameBooking $booking) => $this->mapBookingCard($booking)
                )
            );
        }

        if (! $selectedType || $selectedType === 'beach-event') {
            $bookings = $bookings->merge(
                $this->buildBeachEventQuery($user, $filters)->get()->map(
                    fn (BeachEventBooking $booking) => $this->mapBookingCard($booking)
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

        if (! empty($filters['receipt_search'])) {
            $search = trim($filters['receipt_search']);
            $receiptsQuery->where('invoice_number', 'like', '%'.$search.'%');
        }

        if (! empty($filters['receipt_status'])) {
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
        return $this->showBooking(
            $hotelBooking,
            ['room.hotel', 'invoice'],
            route('bookings.hotels.cancel', $hotelBooking),
            route('bookings.hotels.reschedule', $hotelBooking)
        );
    }

    public function showFerry(FerryBooking $ferryBooking)
    {
        return $this->showBooking(
            $ferryBooking,
            ['ferry.island', 'invoice'],
            route('bookings.ferries.cancel', $ferryBooking),
            route('bookings.ferries.reschedule', $ferryBooking),
            route('bookings.ferries.pass', $ferryBooking)
        );
    }

    public function showRide(RideBooking $rideBooking)
    {
        return $this->showBooking(
            $rideBooking,
            ['ride.island', 'invoice'],
            route('bookings.rides.cancel', $rideBooking),
            route('bookings.rides.reschedule', $rideBooking)
        );
    }

    public function showGame(GameBooking $gameBooking)
    {
        return $this->showBooking(
            $gameBooking,
            ['game.island', 'invoice'],
            route('bookings.games.cancel', $gameBooking),
            route('bookings.games.reschedule', $gameBooking)
        );
    }

    public function showBeachEvent(BeachEventBooking $beachEventBooking)
    {
        return $this->showBooking(
            $beachEventBooking,
            ['beachEvent.island', 'invoice'],
            route('bookings.beach-events.cancel', $beachEventBooking),
            route('bookings.beach-events.reschedule', $beachEventBooking)
        );
    }

    public function cancelHotel(HotelBooking $hotelBooking, BookingLifecycleService $bookingLifecycleService)
    {
        return $this->cancelBooking($hotelBooking, $bookingLifecycleService, 'Hotel booking canceled.');
    }

    public function cancelFerry(FerryBooking $ferryBooking, BookingLifecycleService $bookingLifecycleService)
    {
        return $this->cancelBooking($ferryBooking, $bookingLifecycleService, 'Ferry booking canceled.');
    }

    public function cancelRide(RideBooking $rideBooking, BookingLifecycleService $bookingLifecycleService)
    {
        return $this->cancelBooking($rideBooking, $bookingLifecycleService, 'Ride booking canceled.');
    }

    public function cancelGame(GameBooking $gameBooking, BookingLifecycleService $bookingLifecycleService)
    {
        return $this->cancelBooking($gameBooking, $bookingLifecycleService, 'Game booking canceled.');
    }

    public function cancelBeachEvent(BeachEventBooking $beachEventBooking, BookingLifecycleService $bookingLifecycleService)
    {
        return $this->cancelBooking($beachEventBooking, $bookingLifecycleService, 'Beach event booking canceled.');
    }

    public function rescheduleHotel(
        Request $request,
        HotelBooking $hotelBooking,
        BookingLifecycleService $bookingLifecycleService
    ) {
        return $this->rescheduleBooking(
            $request,
            $hotelBooking,
            $bookingLifecycleService,
            [
                'start_date' => ['required', 'date'],
                'end_date' => ['required', 'date', 'after:start_date'],
            ],
            'Hotel booking rescheduled.'
        );
    }

    public function rescheduleFerry(
        Request $request,
        FerryBooking $ferryBooking,
        BookingLifecycleService $bookingLifecycleService
    ) {
        return $this->rescheduleBooking(
            $request,
            $ferryBooking,
            $bookingLifecycleService,
            [
                'booking_time' => ['required', 'date'],
            ],
            'Ferry booking rescheduled.'
        );
    }

    public function rescheduleRide(
        Request $request,
        RideBooking $rideBooking,
        BookingLifecycleService $bookingLifecycleService
    ) {
        return $this->rescheduleBooking(
            $request,
            $rideBooking,
            $bookingLifecycleService,
            [
                'booking_time' => ['required', 'date'],
            ],
            'Ride booking rescheduled.'
        );
    }

    public function rescheduleGame(
        Request $request,
        GameBooking $gameBooking,
        BookingLifecycleService $bookingLifecycleService
    ) {
        return $this->rescheduleBooking(
            $request,
            $gameBooking,
            $bookingLifecycleService,
            [
                'booking_time' => ['required', 'date'],
            ],
            'Game booking rescheduled.'
        );
    }

    public function rescheduleBeachEvent(
        Request $request,
        BeachEventBooking $beachEventBooking,
        BookingLifecycleService $bookingLifecycleService
    ) {
        return $this->rescheduleBooking(
            $request,
            $beachEventBooking,
            $bookingLifecycleService,
            [
                'booking_date' => ['required', 'date'],
                'booking_time' => ['required', 'date_format:H:i'],
            ],
            'Beach event booking rescheduled.'
        );
    }

    private function showBooking(
        Model $booking,
        array $relations,
        string $cancelRoute,
        string $rescheduleRoute,
        ?string $passDownloadUrl = null
    ) {
        $this->authorize('view', $booking);

        $booking->load($relations);

        return view('pages.bookings.show', [
            'booking' => $booking,
            'type' => BookingSupport::typeLabel($booking),
            'invoice' => $booking->invoice,
            'cancelRoute' => $cancelRoute,
            'rescheduleRoute' => $rescheduleRoute,
            'passDownloadUrl' => $booking->status === 'canceled' ? null : $passDownloadUrl,
            'canSelfServiceChange' => BookingSupport::canSelfServiceChange($booking),
            'changeCutoffAt' => BookingSupport::cutoffAt($booking),
            'rescheduleFields' => $this->rescheduleFields($booking),
        ]);
    }

    private function cancelBooking(Model $booking, BookingLifecycleService $bookingLifecycleService, string $message)
    {
        $this->authorize('update', $booking);

        $bookingLifecycleService->cancelBooking($booking, request()->user());

        return back()->with('status', $message);
    }

    private function rescheduleBooking(
        Request $request,
        Model $booking,
        BookingLifecycleService $bookingLifecycleService,
        array $rules,
        string $message
    ) {
        $this->authorize('update', $booking);

        $bookingLifecycleService->rescheduleBooking(
            $booking,
            $request->validate($rules),
            $request->user()
        );

        return back()->with('status', $message);
    }

    private function buildHotelQuery($user, array $filters)
    {
        $query = $user->hotelBookings()->with('room.hotel')->latest();

        if (! empty($filters['search'])) {
            $query->where(function ($builder) use ($filters) {
                $builder->whereHas('room.hotel', function ($inner) use ($filters) {
                    $inner->where('name', 'like', '%'.$filters['search'].'%');
                })->orWhereHas('room', function ($inner) use ($filters) {
                    $inner->where('room_number', 'like', '%'.$filters['search'].'%');
                });
            });
        }

        if (! empty($filters['from'])) {
            $query->whereDate('start_date', '>=', $filters['from']);
        }

        if (! empty($filters['to'])) {
            $query->whereDate('start_date', '<=', $filters['to']);
        }

        return $query;
    }

    private function buildFerryQuery($user, array $filters)
    {
        $query = $user->ferryBookings()->with('ferry')->latest();

        if (! empty($filters['search'])) {
            $query->whereHas('ferry', function ($builder) use ($filters) {
                $builder->where('name', 'like', '%'.$filters['search'].'%');
            });
        }

        if (! empty($filters['from'])) {
            $query->whereDate('booking_time', '>=', $filters['from']);
        }

        if (! empty($filters['to'])) {
            $query->whereDate('booking_time', '<=', $filters['to']);
        }

        return $query;
    }

    private function buildRideQuery($user, array $filters)
    {
        $query = $user->rideBookings()->with('ride')->latest();

        if (! empty($filters['search'])) {
            $query->whereHas('ride', function ($builder) use ($filters) {
                $builder->where('name', 'like', '%'.$filters['search'].'%');
            });
        }

        if (! empty($filters['from'])) {
            $query->whereDate('booking_time', '>=', $filters['from']);
        }

        if (! empty($filters['to'])) {
            $query->whereDate('booking_time', '<=', $filters['to']);
        }

        return $query;
    }

    private function buildGameQuery($user, array $filters)
    {
        $query = $user->gameBookings()->with('game')->latest();

        if (! empty($filters['search'])) {
            $query->whereHas('game', function ($builder) use ($filters) {
                $builder->where('name', 'like', '%'.$filters['search'].'%');
            });
        }

        if (! empty($filters['from'])) {
            $query->whereDate('booking_time', '>=', $filters['from']);
        }

        if (! empty($filters['to'])) {
            $query->whereDate('booking_time', '<=', $filters['to']);
        }

        return $query;
    }

    private function buildBeachEventQuery($user, array $filters)
    {
        $query = $user->beachEventBookings()->with('beachEvent')->latest();

        if (! empty($filters['search'])) {
            $query->whereHas('beachEvent', function ($builder) use ($filters) {
                $builder->where('name', 'like', '%'.$filters['search'].'%');
            });
        }

        if (! empty($filters['from'])) {
            $query->whereDate('booking_date', '>=', $filters['from']);
        }

        if (! empty($filters['to'])) {
            $query->whereDate('booking_date', '<=', $filters['to']);
        }

        return $query;
    }

    private function mapBookingCard(Model $booking): array
    {
        return [
            'id' => $booking->id,
            'type' => BookingSupport::typeKey($booking),
            'type_label' => BookingSupport::typeLabel($booking),
            'status' => $booking->status,
            'title' => BookingSupport::title($booking),
            'subtitle' => $booking instanceof HotelBooking
                ? 'Room '.($booking->room->room_number ?? 'N/A')
                : (BookingSupport::typeLabel($booking).' booking'),
            'schedule' => BookingSupport::scheduleLabel($booking),
            'quantity' => $booking->quantity,
            'total_price' => (float) $booking->total_price,
            'detail_url' => $this->detailRoute($booking),
            'cancel_url' => $this->cancelRoute($booking),
            'can_cancel' => BookingSupport::canSelfServiceChange($booking),
            'sort_at' => BookingSupport::startAt($booking),
        ];
    }

    private function detailRoute(Model $booking): string
    {
        return match (true) {
            $booking instanceof HotelBooking => route('bookings.hotels.show', $booking),
            $booking instanceof FerryBooking => route('bookings.ferries.show', $booking),
            $booking instanceof RideBooking => route('bookings.rides.show', $booking),
            $booking instanceof GameBooking => route('bookings.games.show', $booking),
            $booking instanceof BeachEventBooking => route('bookings.beach-events.show', $booking),
            default => route('bookings.index'),
        };
    }

    private function cancelRoute(Model $booking): string
    {
        return match (true) {
            $booking instanceof HotelBooking => route('bookings.hotels.cancel', $booking),
            $booking instanceof FerryBooking => route('bookings.ferries.cancel', $booking),
            $booking instanceof RideBooking => route('bookings.rides.cancel', $booking),
            $booking instanceof GameBooking => route('bookings.games.cancel', $booking),
            $booking instanceof BeachEventBooking => route('bookings.beach-events.cancel', $booking),
            default => route('bookings.index'),
        };
    }

    private function rescheduleFields(Model $booking): array
    {
        return match (true) {
            $booking instanceof HotelBooking => [
                ['name' => 'start_date', 'label' => 'Start date', 'type' => 'date', 'value' => $booking->start_date->toDateString()],
                ['name' => 'end_date', 'label' => 'End date', 'type' => 'date', 'value' => $booking->end_date->toDateString()],
            ],
            $booking instanceof BeachEventBooking => [
                ['name' => 'booking_date', 'label' => 'Event date', 'type' => 'date', 'value' => $booking->booking_date->toDateString()],
                ['name' => 'booking_time', 'label' => 'Event time', 'type' => 'time', 'value' => $booking->booking_time->format('H:i')],
            ],
            default => [
                ['name' => 'booking_time', 'label' => 'Booking time', 'type' => 'datetime-local', 'value' => BookingSupport::startAt($booking)->format('Y-m-d\TH:i')],
            ],
        };
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
