<?php

namespace App\Http\Controllers\Bookings;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Services\BookingCheckoutService;
use Illuminate\Http\Request;

class GameBookingController extends Controller
{
    public function store(
        Request $request,
        Game $game,
        BookingCheckoutService $bookingCheckoutService
    ) {
        $bookingCheckoutService->createGame($request->user(), $game, $request->all());

        return back()->with('status', 'Game booking created.');
    }
}
