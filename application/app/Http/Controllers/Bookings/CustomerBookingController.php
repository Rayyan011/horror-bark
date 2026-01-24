<?php

namespace App\Http\Controllers\Bookings;

use App\Http\Controllers\Controller;
use App\Models\BeachEventBooking;
use App\Models\FerryBooking;
use App\Models\GameBooking;
use App\Models\HotelBooking;
use App\Models\RideBooking;
use Illuminate\Http\Request;

class CustomerBookingController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $hotelBookings = $user->hotelBookings()
            ->with('room.hotel')
            ->latest()
            ->get();

        $ferryBookings = $user->ferryBookings()
            ->with('ferry')
            ->latest()
            ->get();

        $rideBookings = $user->rideBookings()
            ->with('ride')
            ->latest()
            ->get();

        $gameBookings = $user->gameBookings()
            ->with('game')
            ->latest()
            ->get();

        $beachEventBookings = $user->beachEventBookings()
            ->with('beachEvent')
            ->latest()
            ->get();

        return view('pages.bookings.index', compact(
            'hotelBookings',
            'ferryBookings',
            'rideBookings',
            'gameBookings',
            'beachEventBookings'
        ));
    }

    public function cancelHotel(HotelBooking $hotelBooking)
    {
        $this->authorize('update', $hotelBooking);

        if ($hotelBooking->status !== 'canceled') {
            $hotelBooking->update(['status' => 'canceled']);
        }

        return back()->with('status', 'Hotel booking canceled.');
    }

    public function cancelFerry(FerryBooking $ferryBooking)
    {
        $this->authorize('update', $ferryBooking);

        if ($ferryBooking->status !== 'canceled') {
            $ferryBooking->update(['status' => 'canceled']);
        }

        return back()->with('status', 'Ferry booking canceled.');
    }

    public function cancelRide(RideBooking $rideBooking)
    {
        $this->authorize('update', $rideBooking);

        if ($rideBooking->status !== 'canceled') {
            $rideBooking->update(['status' => 'canceled']);
        }

        return back()->with('status', 'Ride booking canceled.');
    }

    public function cancelGame(GameBooking $gameBooking)
    {
        $this->authorize('update', $gameBooking);

        if ($gameBooking->status !== 'canceled') {
            $gameBooking->update(['status' => 'canceled']);
        }

        return back()->with('status', 'Game booking canceled.');
    }

    public function cancelBeachEvent(BeachEventBooking $beachEventBooking)
    {
        $this->authorize('update', $beachEventBooking);

        if ($beachEventBooking->status !== 'canceled') {
            $beachEventBooking->update(['status' => 'canceled']);
        }

        return back()->with('status', 'Beach event booking canceled.');
    }
}
