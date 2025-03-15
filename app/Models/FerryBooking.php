<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class FerryBooking extends Model
{
    protected $fillable = [
        'user_id',
        'ferry_id',
        'booking_time',   // Datetime: must start on the hour between 9:00 and 16:00.
        'quantity',
        'total_price',
        'status',
    ];

    public function user(): BelongsTo 
    {
        return $this->belongsTo(User::class);
    }

    public function ferry(): BelongsTo 
    {
        return $this->belongsTo(Ferry::class);
    }

    public function isPending(): bool 
    {
        return $this->status === 'pending';
    }

    protected static function booted()
    {
        static::creating(function (FerryBooking $booking) {
            // Parse booking time and enforce allowed hours.
            $bookingDateTime = Carbon::parse($booking->booking_time);
            $hour = $bookingDateTime->hour;
            if ($hour < 9 || $hour >= 17) {
                throw new \Exception("Ferry bookings must start on the hour between 9 AM and 5 PM.");
            }

            // Retrieve ferry details and its island.
            $ferry = $booking->ferry;
            $island = $ferry->island;

            // Check per-booking quantity limit.
            if ($booking->quantity > $ferry->max_booking_quantity) {
                throw new \Exception("You cannot book more than {$ferry->max_booking_quantity} per booking.");
            }

            // Check overall capacity at that time slot.
            $bookedCount = self::where('ferry_id', $booking->ferry_id)
                ->where('booking_time', $booking->booking_time)
                ->sum('quantity');
            if (($bookedCount + $booking->quantity) > $ferry->max_capacity) {
                throw new \Exception("This booking exceeds the ferry's capacity for the selected time.");
            }

            // Eligibility check based on the island's type.
            if ($island->type === 'theme_park') {
                // User must have an active, confirmed hotel booking.
                $hasHotelBooking = \App\Models\HotelBooking::where('user_id', $booking->user_id)
                    ->where('status', 'confirmed')
                    ->where('start_date', '<=', $bookingDateTime)
                    ->where('end_date', '>', $bookingDateTime)
                    ->exists();
                if (! $hasHotelBooking) {
                    throw new \Exception("You must have an active confirmed hotel booking to reach the theme park island.");
                }
            } elseif ($island->type === 'picnic') {
                // User must have a confirmed beach event booking on the same date.
                $hasBeachEventBooking = \App\Models\BeachEventBooking::where('user_id', $booking->user_id)
                    ->where('status', 'confirmed')
                    ->whereDate('booking_date', $bookingDateTime->toDateString())
                    ->exists();
                if (! $hasBeachEventBooking) {
                    throw new \Exception("You must have a confirmed beach event booking to reach the picnic island.");
                }
            }

            // Calculate total price.
            $booking->total_price = $ferry->price * $booking->quantity;
        });

        static::updating(function (FerryBooking $booking) {
            $ferry = $booking->ferry;
            $booking->total_price = $ferry->price * $booking->quantity;
        });
    }
}
