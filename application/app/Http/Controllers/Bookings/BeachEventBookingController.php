<?php

namespace App\Http\Controllers\Bookings;

use App\Http\Controllers\Controller;
use App\Models\BeachEvent;
use App\Models\BeachEventBooking;
use App\Services\BookingLifecycleService;
use App\Services\IslandAccessService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BeachEventBookingController extends Controller
{
    public function store(
        Request $request,
        BeachEvent $beachEvent,
        BookingLifecycleService $bookingLifecycleService,
        IslandAccessService $islandAccessService
    ) {
        $data = $request->validate([
            'booking_date' => ['required', 'date'],
            'booking_time' => ['required', 'date_format:H:i'],
            'quantity' => ['required', 'integer', 'min:1', 'max:'.$beachEvent->max_booking_quantity],
        ]);

        $bookingDate = Carbon::parse($data['booking_date'])->toDateString();
        $eventDate = Carbon::parse($beachEvent->event_date)->toDateString();

        if ($bookingDate !== $eventDate) {
            return back()->withErrors([
                'booking_date' => 'Booking date must match the event date.',
            ]);
        }

        $bookingTime = Carbon::parse($bookingDate.' '.$data['booking_time'])->setSecond(0);

        $beachEvent->loadMissing('island');

        if (
            $islandAccessService->beachEventRequiresHotel($beachEvent)
            && ! $islandAccessService->hasConfirmedHotelStayAt($request->user(), $bookingTime)
        ) {
            return back()->withErrors([
                'booking_time' => IslandAccessService::REQUIRED_STAY_ERROR,
            ])->withInput();
        }

        $bookedQuantity = BeachEventBooking::query()
            ->where('beach_event_id', $beachEvent->id)
            ->where('booking_date', $bookingDate)
            ->where('booking_time', $bookingTime)
            ->where('status', '!=', 'canceled')
            ->sum('quantity');

        if ($bookedQuantity + $data['quantity'] > $beachEvent->max_capacity) {
            return back()->withErrors([
                'quantity' => 'Not enough capacity for that time slot.',
            ]);
        }

        $booking = BeachEventBooking::create([
            'user_id' => $request->user()->id,
            'beach_event_id' => $beachEvent->id,
            'booking_date' => $bookingDate,
            'booking_time' => $bookingTime,
            'quantity' => $data['quantity'],
            'total_price' => $beachEvent->price * $data['quantity'],
            'status' => 'confirmed',
        ]);

        $bookingLifecycleService->createConfirmedBooking($booking, $request->user());

        return back()->with('status', 'Beach event booking created.');
    }
}
