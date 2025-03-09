<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\AboutController;

Route::redirect('/', '/home')->name('home');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/{page_name}', [PagesController::class, 'show']);
// Route::get('/', function () {
//     return view('welcome');
// });

