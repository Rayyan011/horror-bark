<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\ContactController; // **ADD THIS LINE:  Import ContactController**
use App\Http\Controllers\BeachEventController;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home');

// **ADD THESE LINES BELOW:**
Route::get('/contact', [ContactController::class, 'create'])->name('contacts.create');
Route::post('/contact', [ContactController::class, 'store'])->name('contacts.store');
Route::get('/beach-events', [BeachEventController::class, 'index'])->name('beach-events.index');

Route::get('/{page_name}', [PagesController::class, 'show'])->name('custom_page'); // Added route name for consistency



