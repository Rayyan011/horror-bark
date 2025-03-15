<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class HotelBooking extends Model
{
    protected $fillable = [
        'user_id',
        'room_id',
        'start_date',
        'end_date',
        'total_price',
        'quantity',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
    protected static function booted()
    {
        static::creating(function (RideBooking $rideBooking) {
            // Validate: Ensure the user has a confirmed hotel booking
            $hasHotelBooking = \App\Models\HotelBooking::where('user_id', $rideBooking->user_id)
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
