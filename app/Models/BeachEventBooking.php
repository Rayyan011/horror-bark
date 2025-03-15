<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class BeachEventBooking extends Model
{
    protected $fillable = [
        'user_id',
        'beach_event_id',
        'booking_date',  // Date of booking (should match event_date)
        'quantity',
        'total_price',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function beachEvent(): BelongsTo
    {
        return $this->belongsTo(BeachEvent::class);
    }

    protected static function booted()
    {
        static::creating(function (BeachEventBooking $booking) {
            $event = $booking->beachEvent;
            $bookingDate = Carbon::parse($booking->booking_date)->toDateString();
            $eventDate = Carbon::parse($event->event_date)->toDateString();

            // Ensure the booking date matches the event date.
            if ($bookingDate !== $eventDate) {
                throw new \Exception("The booking date must match the beach event date.");
            }

            // Check per-booking limit.
            if ($booking->quantity > $event->max_booking_quantity) {
                throw new \Exception("You cannot book more than {$event->max_booking_quantity} per booking for this event.");
            }

            // Check overall capacity.
            $bookedCount = self::where('beach_event_id', $booking->beach_event_id)
                ->whereDate('booking_date', $bookingDate)
                ->sum('quantity');
            if (($bookedCount + $booking->quantity) > $event->max_capacity) {
                throw new \Exception("This booking exceeds the event's capacity.");
            }

            // Calculate total price.
            $booking->total_price = $event->price * $booking->quantity;
        });

        static::updating(function (BeachEventBooking $booking) {
            $event = $booking->beachEvent;
            $booking->total_price = $event->price * $booking->quantity;
        });
    }
}
