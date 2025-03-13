<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ride extends Model
{
    protected $fillable = [
        'name',
        'description',
        'default_capacity',
        'open_time',
        'close_time',
        // 'user_id' if you want a ride owner
    ];

    public function rideSlots(): HasMany
    {
        return $this->hasMany(RideSlot::class);
    }
}
