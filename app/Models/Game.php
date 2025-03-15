<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Game extends Model
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

    public function gameSlots(): HasMany
    {
        return $this->hasMany(GameSlot::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}