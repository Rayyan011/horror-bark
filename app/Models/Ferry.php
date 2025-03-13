<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ferry extends Model
{
    protected $fillable = [
        'name',
        'route',
        'default_capacity',
        'open_time',
        'close_time',
    ];

    public function ferrySlots(): HasMany
    {
        return $this->hasMany(FerrySlot::class);
    }
}
