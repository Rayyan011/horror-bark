<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\HotelBooking; // If you plan to check "hotel-first" here

class FerryBooking extends Model
{
    protected $fillable = [
        'user_id',
        'ferry_slot_id',
        'total_price',
        'quantity',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ferrySlot(): BelongsTo
    {
        return $this->belongsTo(FerrySlot::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    protected static function booted()
    {
        static::creating(function (FerryBooking $ferryBooking) {
            // Validate: User must have a confirmed hotel booking.
            $hasHotelBooking = $ferryBooking->user->hotelBookings()
                ->where('status', 'confirmed')
                ->exists();

            if (! $hasHotelBooking) {
                throw new \Exception("User must have a confirmed hotel booking before booking a ferry slot.");
            }

            // Validate: Slot capacity check.
            $ferrySlot = $ferryBooking->ferrySlot;
            if ($ferrySlot->getBookedCount() >= $ferrySlot->capacity) {
                throw new \Exception("This ferry slot is at full capacity.");
            }

            // Calculate dynamic price: total_price = ferry's price * quantity.
            $ferryPrice = $ferrySlot->ferry->price ?? 0;
            $ferryBooking->total_price = $ferryPrice * $ferryBooking->quantity;
        });

        static::updating(function (FerryBooking $ferryBooking) {
            $ferrySlot = $ferryBooking->ferrySlot;
            $ferryPrice = $ferrySlot->ferry->price ?? 0;
            $ferryBooking->total_price = $ferryPrice * $ferryBooking->quantity;
        });
    }
}

