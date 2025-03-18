<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ThemeParkController;


Route::redirect('/', '/home')->name('home');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::get('/theme-park', [ThemeParkController::class, 'index'])->name('theme-park');
Route::get('/{page_name}', [PagesController::class, 'show']);
// Route::get('/', function () {
//     return view('welcome');
// });

