<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ferry extends Model
{
    protected $fillable = [
        'user_id',           // Owner of the ferry
        'name',
        'description',
        'price',
        'max_capacity',
        'max_booking_quantity',
        'island_id',         // Foreign key to the Island model
        'map_x',
        'map_y',
        'images',
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
        return $this->hasMany(FerryBooking::class);
    }
}
