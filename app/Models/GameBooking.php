<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameBooking extends Model
{
    protected $fillable = [
        'user_id',
        'game_id',
        'booking_time',
        'total_price',
        'quantity',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

}
