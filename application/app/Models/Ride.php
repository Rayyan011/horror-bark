<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ride extends Model
{
    protected $fillable = [
        'user_id',            // Owner of the ride
        'island_id',
        'name',
        'price',
        'latitude',
        'longitude',
        'images',
        'max_capacity',       // Maximum capacity per booking time slot
        'max_booking_quantity'// Maximum allowed per single booking
    ];

    protected $casts = [
        'images' => 'array',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function island(): BelongsTo
    {
        return $this->belongsTo(Island::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(RideBooking::class);
    }
}
