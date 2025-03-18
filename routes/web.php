<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PagesController;
<<<<<<< HEAD
use App\Http\Controllers\AboutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ThemeParkController;


Route::redirect('/', '/home')->name('home');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::get('/theme-park', [ThemeParkController::class, 'index'])->name('theme-park');
=======

Route::redirect('/', '/home')->name('home');
>>>>>>> 132dbe4e7633d85d880c9b2366a2ee5414c1e904
Route::get('/{page_name}', [PagesController::class, 'show']);
// Route::get('/', function () {
//     return view('welcome');
// });
<<<<<<< HEAD

=======
>>>>>>> 132dbe4e7633d85d880c9b2366a2ee5414c1e904
