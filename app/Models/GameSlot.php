<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GameSlot extends Model
{
    protected $fillable = [
        'game_id',
        'slot_date',
        'start_time',
        'end_time',
        'capacity',
        'status',
    ];

    protected $casts = [
        'slot_date'  => 'date:Y-m-d',
        'start_time' => 'datetime:H:i',
        'end_time'   => 'datetime:H:i',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function gameBookings(): HasMany
    {
        return $this->hasMany(GameBooking::class);
    }

    // Returns the count of non-canceled bookings.
    public function getBookedCount(): int
    {
        return $this->gameBookings()
            ->where('status', '!=', 'canceled')
            ->count();
    }
}
