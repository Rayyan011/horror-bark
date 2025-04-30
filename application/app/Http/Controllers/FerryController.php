<?php

namespace App\Http\Controllers;

use App\Models\Ferry; 
use Illuminate\Http\Request;

class FerryController extends Controller
{
    public function index()
    {
        $ferries = Ferry::all(); 
        return view('pages.ferries.index', compact('ferries'));
    }
}