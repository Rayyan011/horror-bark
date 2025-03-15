<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class RideBooking extends Model
{
    protected $fillable = [
        'user_id',
        'ride_id',
        'booking_time',  // datetime field: allowed only at 9:00 or 17:00
        'quantity',
        'total_price',
        'status'
    ];

    public function user(): BelongsTo 
    {
        return $this->belongsTo(User::class);
    }

    public function ride(): BelongsTo 
    {
        return $this->belongsTo(Ride::class);
    }

    public function isPending(): bool 
    {
        return $this->status === 'pending';
    }

    protected static function booted()
    {
        static::creating(function (RideBooking $booking) {
            // Require an active confirmed hotel booking.
            $now = Carbon::now();
            $activeHotelBooking = \App\Models\HotelBooking::where('user_id', $booking->user_id)
                ->where('status', 'confirmed')
                ->where('start_date', '<=', $now)
                ->where('end_date', '>=', $now)
                ->exists();
            if (! $activeHotelBooking) {
                throw new \Exception("User must have an active confirmed hotel booking before booking a ride.");
            }

            // Enforce allowed booking hours: 9:00 AM or 5:00 PM.
            $startTime = Carbon::parse($booking->booking_time);
            if (! in_array($startTime->hour, [9, 17])) {
                throw new \Exception("Bookings are only allowed at 9:00 AM or 5:00 PM.");
            }

            // Check per-booking limit.
            $ride = $booking->ride;
            if ($booking->quantity > $ride->max_booking_quantity) {
                throw new \Exception("Cannot book more than {$ride->max_booking_quantity} per booking.");
            }

            // Check overall capacity for that booking time.
            $bookedCount = self::where('ride_id', $booking->ride_id)
                ->where('booking_time', $booking->booking_time)
                ->sum('quantity');
            if (($bookedCount + $booking->quantity) > $ride->max_capacity) {
                throw new \Exception("Booking exceeds ride capacity.");
            }

            // Calculate total price.
            $booking->total_price = $ride->price * $booking->quantity;
        });

        static::updating(function (RideBooking $booking) {
            $ride = $booking->ride;
            $booking->total_price = $ride->price * $booking->quantity;
        });
    }
}
