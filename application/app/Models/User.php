<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\HotelBooking;
use App\Models\FerryBooking;
use App\Models\RideBooking;
use App\Models\GameBooking;
use App\Models\BeachEventBooking;
use App\Models\Hotel;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function hotelBookings(): HasMany
    {
        return $this->hasMany(HotelBooking::class);
    }

    public function ferryBookings(): HasMany
    {
        return $this->hasMany(FerryBooking::class);
    }

    public function rideBookings(): HasMany
    {
        return $this->hasMany(RideBooking::class);
    }

    public function gameBookings(): HasMany
    {
        return $this->hasMany(GameBooking::class);
    }

    public function beachEventBookings(): HasMany
    {
        return $this->hasMany(BeachEventBooking::class);
    }

    public function hotels(): HasMany
    {
        return $this->hasMany(Hotel::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($this->hasRole('super_admin')) {
            return true;
        }

        return match ($panel->getId()) {
            'admin' => $this->hasRole('admin'),
            'hotel' => $this->hasRole('hotel_manager'),
            'ferry' => $this->hasRole('ferry_manager'),
            'ride' => $this->hasRole('ride_manager'),
            'game' => $this->hasRole('game_manager'),
            'user' => $this->hasRole('user'),
            default => false,
        };
    }
}
