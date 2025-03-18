<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ThemeParkController extends Controller
{
    public function index()
    {
        return view('theme-park'); // Returns the 'theme-park.blade.php' view
    }
}