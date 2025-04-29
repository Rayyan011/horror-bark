<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    public function index()
    {
        $hotels = Hotel::all(); // get all hotels from database
        return view('pages.hotels.index', compact('hotels')); // send to Blade
    }

    public function show(Hotel $hotel)
    {
        $hotel->load('rooms'); // get hotel and its rooms
        return view('pages.hotels.show', compact('hotel'));
    }
}