<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model
{
    protected $fillable = [
        'user_id',            // Owner of the game
        'island_id',
        'name',
        'description',
        'location',
        'price',
        'latitude',
        'longitude',
        'map_x',
        'map_y',
        'images',
        'max_capacity',
        'max_booking_quantity'
    ];

    protected $casts = [
        'images' => 'array',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function island(): BelongsTo
    {
        return $this->belongsTo(Island::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(GameBooking::class);
    }
}
