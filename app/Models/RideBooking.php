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
        'quantity',
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
            // Validate: User must have a confirmed hotel booking
            // (Assumes your User model has a hotelBookings() relationship.)
            $hasHotelBooking = $rideBooking->user->hotelBookings()
                ->where('status', 'confirmed')
                ->exists();

            if (! $hasHotelBooking) {
                throw new \Exception("User must have a confirmed hotel booking before booking a ride slot.");
            }

            // Validate: Slot capacity check
            $rideSlot = $rideBooking->rideSlot;
            if ($rideSlot->getBookedCount() >= $rideSlot->capacity) {
                throw new \Exception("This ride slot is at full capacity.");
            }

            // Dynamic price calculation: total_price = ride's price * quantity
            $ridePrice = $rideSlot->ride->price ?? 0;
            $rideBooking->total_price = $ridePrice * $rideBooking->quantity;
        });

        static::updating(function (RideBooking $rideBooking) {
            // Recalculate total price when updating booking (if quantity or slot changes)
            $rideSlot = $rideBooking->rideSlot;
            $ridePrice = $rideSlot->ride->price ?? 0;
            $rideBooking->total_price = $ridePrice * $rideBooking->quantity;
        });
    }
}
