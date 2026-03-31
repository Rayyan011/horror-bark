<?php

namespace App\Http\Controllers\Bookings;

use App\Http\Controllers\Controller;
use App\Models\HotelBooking;
use App\Models\Room;
use App\Services\BookingLifecycleService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HotelBookingController extends Controller
{
    public function store(Request $request, Room $room, BookingLifecycleService $bookingLifecycleService)
    {
        $data = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'quantity' => ['required', 'integer', 'min:1', 'max:'.$room->max_occupancy],
        ]);

        $startDate = Carbon::parse($data['start_date'])->startOfDay();
        $endDate = Carbon::parse($data['end_date'])->startOfDay();
        $nights = max(1, $startDate->diffInDays($endDate));

        $overlappingQuantity = HotelBooking::query()
            ->where('room_id', $room->id)
            ->where('status', '!=', 'canceled')
            ->where(function ($query) use ($startDate, $endDate) {
                $query
                    ->where('start_date', '<', $endDate)
                    ->where('end_date', '>', $startDate);
            })
            ->sum('quantity');

        if ($overlappingQuantity + $data['quantity'] > $room->max_occupancy) {
            return back()->withErrors([
                'quantity' => 'Not enough availability for the selected dates.',
            ]);
        }

        $booking = HotelBooking::create([
            'user_id' => $request->user()->id,
            'room_id' => $room->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'quantity' => $data['quantity'],
            'total_price' => $room->price * $data['quantity'] * $nights,
            'status' => 'confirmed',
        ]);

        $bookingLifecycleService->createConfirmedBooking($booking, $request->user());

        return back()->with('status', 'Hotel booking created.');
    }
}
