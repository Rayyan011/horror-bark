<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ride extends Model
{
    protected $fillable = [
        'name',
        'description',
        'default_capacity',
        'open_time',
        'close_time',
       
    ];

    protected $casts = [
        'open_time'  => 'datetime:H:i',
        'close_time' => 'datetime:H:i',
    ];

    public function rideSlots(): HasMany
    {
        return $this->hasMany(RideSlot::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
}
