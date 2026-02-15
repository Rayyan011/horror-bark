<?php

namespace App\Http\Controllers\Bookings;

use App\Http\Controllers\Controller;
use App\Models\Ferry;
use App\Models\FerryBooking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\InvoiceService;
use App\Services\IslandAccessService;

class FerryBookingController extends Controller
{
    public function store(
        Request $request,
        Ferry $ferry,
        InvoiceService $invoiceService,
        IslandAccessService $islandAccessService
    )
    {
        $data = $request->validate([
            'booking_time' => ['required', 'date'],
            'quantity' => ['required', 'integer', 'min:1', 'max:' . $ferry->max_booking_quantity],
        ]);

        $bookingTime = Carbon::parse($data['booking_time'])->setSecond(0);
        $hour = (int) $bookingTime->format('G');

        if ($bookingTime->minute !== 0 || $hour < 9 || $hour > 16) {
            return back()->withErrors([
                'booking_time' => 'Ferry bookings must start on the hour between 9:00 and 16:00.',
            ]);
        }

        $ferry->loadMissing('island');

        if (
            $islandAccessService->ferryRequiresHotel($ferry)
            && !$islandAccessService->hasConfirmedHotelStayAt($request->user(), $bookingTime)
        ) {
            return back()->withErrors([
                'booking_time' => IslandAccessService::REQUIRED_STAY_ERROR,
            ])->withInput();
        }

        $bookedQuantity = FerryBooking::query()
            ->where('ferry_id', $ferry->id)
            ->where('booking_time', $bookingTime)
            ->where('status', '!=', 'canceled')
            ->sum('quantity');

        if ($bookedQuantity + $data['quantity'] > $ferry->max_capacity) {
            return back()->withErrors([
                'quantity' => 'Not enough capacity for that time slot.',
            ]);
        }

        $booking = FerryBooking::create([
            'user_id' => $request->user()->id,
            'ferry_id' => $ferry->id,
            'booking_time' => $bookingTime,
            'quantity' => $data['quantity'],
            'total_price' => $ferry->price * $data['quantity'],
            'status' => 'confirmed',
        ]);

        $invoiceService->createForBooking($booking, $request->user()->id, (float) $booking->total_price);

        return back()->with('status', 'Ferry booking created.');
    }
}
