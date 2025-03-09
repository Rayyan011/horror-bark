<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller; // Make sure this is here
use Illuminate\Http\Request;

class AboutController extends Controller
{
    public function index()
    {
        return view('about'); // Return the 'about' view
    }
}