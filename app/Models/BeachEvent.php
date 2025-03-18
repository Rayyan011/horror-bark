<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BeachEvent extends Model
{
    protected $fillable = [
        'user_id',    // Organizer of the event
        'name',
        'event_date', // Date of the event (YYYY-MM-DD)
        'price',
        'max_capacity',
        'max_booking_quantity',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(BeachEventBooking::class);
    }
}
