<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PagesController;

Route::redirect('/', '/home')->name('home');
Route::get('/{page_name}', [PagesController::class, 'show']);
// Route::get('/', function () {
//     return view('welcome');
// });
