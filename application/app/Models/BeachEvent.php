<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BeachEvent extends Model
{
    protected $fillable = [
        'user_id',    // Organizer of the event
        'island_id',
        'name',
        'event_date', // Date of the event (YYYY-MM-DD)
        'price',
        'latitude',
        'longitude',
        'images',
        'max_capacity',
        'max_booking_quantity',
    ];

    protected $casts = [
        'images' => 'array',
        'event_date' => 'date',
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
        return $this->hasMany(BeachEventBooking::class);
    }
}
