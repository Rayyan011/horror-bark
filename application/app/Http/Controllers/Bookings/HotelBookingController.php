<?php

namespace App\Http\Controllers\Bookings;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Services\BookingCheckoutService;
use Illuminate\Http\Request;

class HotelBookingController extends Controller
{
    public function store(Request $request, Room $room, BookingCheckoutService $bookingCheckoutService)
    {
        $bookingCheckoutService->createHotel($request->user(), $room, $request->all());

        return back()->with('status', 'Hotel booking created.');
    }
}
