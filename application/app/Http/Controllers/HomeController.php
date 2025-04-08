<?php

namespace App\Http\Controllers;

use App\Models\Hotel;

class HomeController extends Controller
{
    public function index()
    {
        $hotels = Hotel::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        return view('pages.home', compact('hotels'));
    }
}
