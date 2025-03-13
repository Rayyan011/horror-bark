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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            // Example "hotel-first" check:
            // Make sure user has a confirmed hotel booking
            $user = $booking->user;
            $hasHotelBooking = $user->hotelBookings()
                ->where('status', 'confirmed')
                ->exists();
            if (! $hasHotelBooking) {
                throw new \Exception("User must have a confirmed hotel booking before booking the ferry.");
            }

            // Capacity check for ferry slot
            $slot = $booking->ferrySlot;
            if ($slot->getBookedCount() >= $slot->capacity) {
                throw new \Exception("This ferry slot is at full capacity.");
            }
        });
    }
}