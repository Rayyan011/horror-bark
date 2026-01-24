<?php

namespace App\Http\Controllers\Bookings;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\GameBooking;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GameBookingController extends Controller
{
    public function store(Request $request, Game $game)
    {
        $data = $request->validate([
            'booking_time' => ['required', 'date'],
            'quantity' => ['required', 'integer', 'min:1', 'max:' . $game->max_booking_quantity],
        ]);

        $bookingTime = Carbon::parse($data['booking_time'])->setSecond(0);
        $hour = (int) $bookingTime->format('G');

        if ($bookingTime->minute !== 0 || !in_array($hour, [9, 17], true)) {
            return back()->withErrors([
                'booking_time' => 'Game bookings are only available at 9:00 or 17:00.',
            ]);
        }

        $bookedQuantity = GameBooking::query()
            ->where('game_id', $game->id)
            ->where('booking_time', $bookingTime)
            ->where('status', '!=', 'canceled')
            ->sum('quantity');

        if ($bookedQuantity + $data['quantity'] > $game->max_capacity) {
            return back()->withErrors([
                'quantity' => 'Not enough capacity for that time slot.',
            ]);
        }

        GameBooking::create([
            'user_id' => $request->user()->id,
            'game_id' => $game->id,
            'booking_time' => $bookingTime,
            'quantity' => $data['quantity'],
            'total_price' => $game->price * $data['quantity'],
            'status' => 'pending',
        ]);

        return back()->with('status', 'Game booking created.');
    }
}
