<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RideBooking extends Model
{
    protected $fillable = [
        'user_id',
        'ride_slot_id',
        'total_price',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function rideSlot(): BelongsTo
    {
        return $this->belongsTo(RideSlot::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    protected static function booted()
{
    static::creating(function (RideBooking $rideBooking) {
        // Check if the user has at least one "confirmed" hotel booking
        $hasHotelBooking = $rideBooking->user->hotelBookings()
            ->where('status', 'confirmed')
            ->exists();

        if (! $hasHotelBooking) {
            throw new \Exception("User must have a confirmed hotel booking before booking a ride slot.");
        }

        // Check the slot capacity
        $rideSlot = $rideBooking->rideSlot;
        if ($rideSlot->getBookedCount() >= $rideSlot->capacity) {
            throw new \Exception("This ride slot is at full capacity.");
        }
    });
}
}
