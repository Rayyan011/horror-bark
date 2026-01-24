<?php

namespace App\Http\Controllers\Bookings;

use App\Http\Controllers\Controller;
use App\Models\Ride;
use App\Models\RideBooking;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RideBookingController extends Controller
{
    public function store(Request $request, Ride $ride)
    {
        $data = $request->validate([
            'booking_time' => ['required', 'date'],
            'quantity' => ['required', 'integer', 'min:1', 'max:' . $ride->max_booking_quantity],
        ]);

        $bookingTime = Carbon::parse($data['booking_time'])->setSecond(0);
        $hour = (int) $bookingTime->format('G');

        if ($bookingTime->minute !== 0 || !in_array($hour, [9, 17], true)) {
            return back()->withErrors([
                'booking_time' => 'Ride bookings are only available at 9:00 or 17:00.',
            ]);
        }

        $bookedQuantity = RideBooking::query()
            ->where('ride_id', $ride->id)
            ->where('booking_time', $bookingTime)
            ->where('status', '!=', 'canceled')
            ->sum('quantity');

        if ($bookedQuantity + $data['quantity'] > $ride->max_capacity) {
            return back()->withErrors([
                'quantity' => 'Not enough capacity for that time slot.',
            ]);
        }

        RideBooking::create([
            'user_id' => $request->user()->id,
            'ride_id' => $ride->id,
            'booking_time' => $bookingTime,
            'quantity' => $data['quantity'],
            'total_price' => $ride->price * $data['quantity'],
            'status' => 'pending',
        ]);

        return back()->with('status', 'Ride booking created.');
    }
}
