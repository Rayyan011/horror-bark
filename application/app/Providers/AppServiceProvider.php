<?php

namespace App\Providers;

use App\Models\BeachEvent;
use App\Models\BeachEventBooking;
use App\Models\Ferry;
use App\Models\FerryBooking;
use App\Models\Game;
use App\Models\GameBooking;
use App\Models\Hotel;
use App\Models\HotelBooking;
use App\Models\Page;
use App\Models\Promotion;
use App\Models\Ride;
use App\Models\RideBooking;
use App\Models\Room;
use App\Observers\AuditableModelObserver;
use App\Policies\BeachEventBookingPolicy;
use App\Policies\FerryBookingPolicy;
use App\Policies\GameBookingPolicy;
use App\Policies\HotelBookingPolicy;
use App\Policies\RideBookingPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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

        Hotel::observe(AuditableModelObserver::class);
        Room::observe(AuditableModelObserver::class);
        Ferry::observe(AuditableModelObserver::class);
        Ride::observe(AuditableModelObserver::class);
        Game::observe(AuditableModelObserver::class);
        BeachEvent::observe(AuditableModelObserver::class);
        Promotion::observe(AuditableModelObserver::class);
        Page::observe(AuditableModelObserver::class);
    }
}
