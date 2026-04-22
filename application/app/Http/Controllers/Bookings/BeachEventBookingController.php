<?php

namespace App\Http\Controllers\Bookings;

use App\Http\Controllers\Controller;
use App\Models\BeachEvent;
use App\Services\BookingCheckoutService;
use Illuminate\Http\Request;

class BeachEventBookingController extends Controller
{
    public function store(
        Request $request,
        BeachEvent $beachEvent,
        BookingCheckoutService $bookingCheckoutService
    ) {
        $bookingCheckoutService->createBeachEvent($request->user(), $beachEvent, $request->all());

        return back()->with('status', 'Beach event booking created.');
    }
}
