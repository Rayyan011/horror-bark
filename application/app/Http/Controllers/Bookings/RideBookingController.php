<?php

namespace App\Http\Controllers\Bookings;

use App\Http\Controllers\Controller;
use App\Models\Ride;
use App\Services\BookingCheckoutService;
use Illuminate\Http\Request;

class RideBookingController extends Controller
{
    public function store(
        Request $request,
        Ride $ride,
        BookingCheckoutService $bookingCheckoutService
    ) {
        $bookingCheckoutService->createRide($request->user(), $ride, $request->all());

        return back()->with('status', 'Ride booking created.');
    }
}
