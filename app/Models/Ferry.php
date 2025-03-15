<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ferry extends Model
{
    protected $fillable = [
        'name',
        'description',
        'default_capacity',
        'open_time',
        'close_time',
        'price',
    ];

    protected $casts = [
        'open_time'  => 'datetime:H:i',
        'close_time' => 'datetime:H:i',
    ];

    public function ferrySlots(): HasMany
    {
        return $this->hasMany(FerrySlot::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}