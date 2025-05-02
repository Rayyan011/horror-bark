<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    protected $fillable = [
        'hotel_id',
        'room_number',
        'price',
        'status',
        'max_occupancy', // Maximum number of people allowed
        'amenities',
        'images',        // JSON field: list of image URLs or paths
        'description'
    ];
    protected $casts = [
        'amenities' => 'array',
        'images'    => 'array',
    ];
    
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function hotelBookings(): HasMany
    {
        return $this->hasMany(HotelBooking::class);
    }
}
