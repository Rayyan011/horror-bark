<?php

namespace App\Http\Controllers\Bookings;

use App\Http\Controllers\Controller;
use App\Models\Ferry;
use App\Services\BookingCheckoutService;
use Illuminate\Http\Request;

class FerryBookingController extends Controller
{
    public function store(
        Request $request,
        Ferry $ferry,
        BookingCheckoutService $bookingCheckoutService
    ) {
        $bookingCheckoutService->createFerry($request->user(), $ferry, $request->all());

        return back()->with('status', 'Ferry booking created.');
    }
}
