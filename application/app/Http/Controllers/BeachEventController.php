<?php

namespace App\Http\Controllers;

use App\Models\BeachEvent;
use Illuminate\Http\Request;

class BeachEventController extends Controller
{
    public function index()
    {
        $beachEvents = BeachEvent::with('owner')->get();

        return view('pages.beach-events.index', compact('beachEvents'));
    }
}