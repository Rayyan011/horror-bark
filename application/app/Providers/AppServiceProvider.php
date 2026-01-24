<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\HotelBooking;
use App\Models\FerryBooking;
use App\Models\RideBooking;
use App\Models\GameBooking;
use App\Models\BeachEventBooking;
use App\Policies\HotelBookingPolicy;
use App\Policies\FerryBookingPolicy;
use App\Policies\RideBookingPolicy;
use App\Policies\GameBookingPolicy;
use App\Policies\BeachEventBookingPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(HotelBooking::class, HotelBookingPolicy::class);
        Gate::policy(FerryBooking::class, FerryBookingPolicy::class);
        Gate::policy(RideBooking::class, RideBookingPolicy::class);
        Gate::policy(GameBooking::class, GameBookingPolicy::class);
        Gate::policy(BeachEventBooking::class, BeachEventBookingPolicy::class);
    }
}
