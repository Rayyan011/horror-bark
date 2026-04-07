<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hotel extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'location',
        'description',
        'latitude',
        'longitude',
        'map_x',
        'map_y',
        'images',
    ];

    protected $casts = [
        'images' => 'array',
    ];

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
