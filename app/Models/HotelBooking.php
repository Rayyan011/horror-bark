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

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function calculateTotalPrice(): float
    {
        $roomPrice = $this->room->price;
        $start = Carbon::parse($this->start_date);
        $end = Carbon::parse($this->end_date);
        $days = $start->diffInDays($end);
        $quantity = $this->quantity ?: 1;
        return $roomPrice * $days * $quantity;
    }

    /**
     * Automatically calculate and set the total price before saving.
     */
    protected static function booted()
    {
        static::saving(function ($booking) {
            $booking->total_price = $booking->calculateTotalPrice();
        });
    }
}
