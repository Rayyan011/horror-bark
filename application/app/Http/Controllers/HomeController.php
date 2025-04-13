<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Island;
use App\Models\Ride;
use App\Models\Game;
use App\Models\BeachEvent;

class HomeController extends Controller
{
    public function index()
    {
        $hotels = Hotel::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        $islands = Island::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        $rides = Ride::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        $games = Game::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        $beachEvents = BeachEvent::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        return view('pages.home', compact('hotels', 'islands', 'rides', 'games', 'beachEvents'));
    }
}
