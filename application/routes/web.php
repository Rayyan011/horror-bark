<?php

use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BeachEventController;
use App\Http\Controllers\Bookings\BeachEventBookingController;
use App\Http\Controllers\Bookings\CustomerBookingController;
use App\Http\Controllers\Bookings\FerryBookingController;
use App\Http\Controllers\Bookings\GameBookingController;
use App\Http\Controllers\Bookings\HotelBookingController;
use App\Http\Controllers\Bookings\RideBookingController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FerryController;
use App\Http\Controllers\FerryOperatorReportController;
use App\Http\Controllers\FerryPassController;
use App\Http\Controllers\GeneratedMediaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OperatorReportController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ThemeParkController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/contact', [ContactController::class, 'create'])->name('contacts.create');
Route::post('/contact', [ContactController::class, 'store'])->name('contacts.store');
Route::view('/about', 'pages.about')->name('about');
Route::get('/beach-events', [BeachEventController::class, 'index'])->name('beach-events.index');

Route::get('/hotels', [HotelController::class, 'index'])->name('hotels.index'); // Specific /hotels route first
Route::get('/hotels/{hotel}', [HotelController::class, 'show'])->name('hotels.show');
Route::get('/ferrytickets', [FerryController::class, 'index'])->name('ferries.index');
Route::get('/themepark', [ThemeParkController::class, 'index'])->name('themepark.index');
Route::get('/generated-media/{collection}/{slug}.svg', GeneratedMediaController::class)->name('generated-media.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/portal', [CustomerBookingController::class, 'index'])->name('portal');
    Route::get('/bookings', [CustomerBookingController::class, 'index'])->name('bookings.index');

    Route::get('/bookings/hotels/{hotelBooking}', [CustomerBookingController::class, 'showHotel'])->name('bookings.hotels.show');
    Route::get('/bookings/ferries/{ferryBooking}', [CustomerBookingController::class, 'showFerry'])->name('bookings.ferries.show');
    Route::get('/bookings/rides/{rideBooking}', [CustomerBookingController::class, 'showRide'])->name('bookings.rides.show');
    Route::get('/bookings/games/{gameBooking}', [CustomerBookingController::class, 'showGame'])->name('bookings.games.show');
    Route::get('/bookings/beach-events/{beachEventBooking}', [CustomerBookingController::class, 'showBeachEvent'])->name('bookings.beach-events.show');
    Route::get('/bookings/ferries/{ferryBooking}/pass', [FerryPassController::class, 'download'])->name('bookings.ferries.pass');

    Route::post('/bookings/hotels/rooms/{room}', [HotelBookingController::class, 'store'])->name('bookings.hotels.store');
    Route::post('/bookings/ferries/{ferry}', [FerryBookingController::class, 'store'])->name('bookings.ferries.store');
    Route::post('/bookings/rides/{ride}', [RideBookingController::class, 'store'])->name('bookings.rides.store');
    Route::post('/bookings/games/{game}', [GameBookingController::class, 'store'])->name('bookings.games.store');
    Route::post('/bookings/beach-events/{beachEvent}', [BeachEventBookingController::class, 'store'])->name('bookings.beach-events.store');

    Route::patch('/bookings/hotels/{hotelBooking}/cancel', [CustomerBookingController::class, 'cancelHotel'])->name('bookings.hotels.cancel');
    Route::patch('/bookings/ferries/{ferryBooking}/cancel', [CustomerBookingController::class, 'cancelFerry'])->name('bookings.ferries.cancel');
    Route::patch('/bookings/rides/{rideBooking}/cancel', [CustomerBookingController::class, 'cancelRide'])->name('bookings.rides.cancel');
    Route::patch('/bookings/games/{gameBooking}/cancel', [CustomerBookingController::class, 'cancelGame'])->name('bookings.games.cancel');
    Route::patch('/bookings/beach-events/{beachEventBooking}/cancel', [CustomerBookingController::class, 'cancelBeachEvent'])->name('bookings.beach-events.cancel');
    Route::patch('/bookings/hotels/{hotelBooking}/reschedule', [CustomerBookingController::class, 'rescheduleHotel'])->name('bookings.hotels.reschedule');
    Route::patch('/bookings/ferries/{ferryBooking}/reschedule', [CustomerBookingController::class, 'rescheduleFerry'])->name('bookings.ferries.reschedule');
    Route::patch('/bookings/rides/{rideBooking}/reschedule', [CustomerBookingController::class, 'rescheduleRide'])->name('bookings.rides.reschedule');
    Route::patch('/bookings/games/{gameBooking}/reschedule', [CustomerBookingController::class, 'rescheduleGame'])->name('bookings.games.reschedule');
    Route::patch('/bookings/beach-events/{beachEventBooking}/reschedule', [CustomerBookingController::class, 'rescheduleBeachEvent'])->name('bookings.beach-events.reschedule');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');

    Route::get('/ferry/passenger-reports', [FerryOperatorReportController::class, 'index'])->name('ferry-reports.index');
    Route::get('/ferry/passenger-reports/export', [FerryOperatorReportController::class, 'export'])->name('ferry-reports.export');
    Route::get('/reports/admin', [AdminReportController::class, 'index'])->name('admin-reports.index');
    Route::get('/reports/admin/export', [AdminReportController::class, 'export'])->name('admin-reports.export');
    Route::get('/reports/{domain}', [OperatorReportController::class, 'index'])
        ->whereIn('domain', ['hotel', 'ferry', 'ride', 'game'])
        ->name('operator-reports.index');
    Route::get('/reports/{domain}/export', [OperatorReportController::class, 'export'])
        ->whereIn('domain', ['hotel', 'ferry', 'ride', 'game'])
        ->name('operator-reports.export');
});

Route::get('/{page_name}', [PagesController::class, 'show'])->name('custom_page'); // Generic route AFTER all specific routes

// ...
