<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameBooking extends Model
{
    protected $fillable = [
        'user_id',
        'game_slot_id',
        'total_price',
        'quantity',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function gameSlot(): BelongsTo
    {
        return $this->belongsTo(GameSlot::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    protected static function booted()
    {
        static::creating(function (GameBooking $gameBooking) {
            // Validate: User must have a confirmed hotel booking.
            $hasHotelBooking = $gameBooking->user->hotelBookings()
                ->where('status', 'confirmed')
                ->exists();

            if (! $hasHotelBooking) {
                throw new \Exception("User must have a confirmed hotel booking before booking a game slot.");
            }

            // Validate: Slot capacity check.
            $gameSlot = $gameBooking->gameSlot;
            if ($gameSlot->getBookedCount() >= $gameSlot->capacity) {
                throw new \Exception("This game slot is at full capacity.");
            }

            // Calculate dynamic price: total_price = game's price * quantity.
            $gamePrice = $gameSlot->game->price ?? 0;
            $gameBooking->total_price = $gamePrice * $gameBooking->quantity;
        });

        static::updating(function (GameBooking $gameBooking) {
            $gameSlot = $gameBooking->gameSlot;
            $gamePrice = $gameSlot->game->price ?? 0;
            $gameBooking->total_price = $gamePrice * $gameBooking->quantity;
        });
    }
}
