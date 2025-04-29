<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\BeachEventController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\FerryController;
use App\Http\Controllers\ThemeParkController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/contact', [ContactController::class, 'create'])->name('contacts.create');
Route::post('/contact', [ContactController::class, 'store'])->name('contacts.store');
Route::get('/beach-events', [BeachEventController::class, 'index'])->name('beach-events.index');

Route::get('/hotels', [HotelController::class, 'index'])->name('hotels.index'); // Specific /hotels route first
Route::get('/hotels/{hotel}', [HotelController::class, 'show'])->name('hotels.show');
Route::get('/ferrytickets', [FerryController::class, 'index'])->name('ferries.index');
Route::get('/themepark', [ThemeParkController::class, 'index'])->name('themepark.index');
Route::get('/{page_name}', [PagesController::class, 'show'])->name('custom_page'); // Generic route AFTER /hotels

// ... other routes ...



// ...