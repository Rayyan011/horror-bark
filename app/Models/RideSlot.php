<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RideSlot extends Model
{
    protected $fillable = [
        'ride_id',
        'slot_date',
        'start_time',
        'end_time',
        'capacity',
        'status',
    ];

    public function ride(): BelongsTo
    {
        return $this->belongsTo(Ride::class);
    }

    public function rideBookings(): HasMany
    {
        return $this->hasMany(RideBooking::class);
    }

    // Example function to count how many bookings exist in this slot
    public function getBookedCount(): int
    {
        return $this->rideBookings()
            ->where('status', '!=', 'canceled')
            ->count();
    }
}
