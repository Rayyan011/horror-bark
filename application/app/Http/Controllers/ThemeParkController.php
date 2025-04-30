<?php

namespace App\Http\Controllers;

use App\Models\Ride; // Import the Ride model
use App\Models\Game; // Import the Game model
use Illuminate\Http\Request;
use Illuminate\View\View;

class ThemeParkController extends Controller
{
    public function index(): View
    {
        // Fetch relevant data for the theme park
        $rides = Ride::all(); // Get all rides
        $games = Game::all(); // Get all games

        // Return the view, passing the rides and games data
        return view('pages.themepark.index', compact('rides', 'games'));
    }

    // You can add methods later for specific rides/games if needed
    // public function showRide(Ride $ride) { ... }
    // public function showGame(Game $game) { ... }
}