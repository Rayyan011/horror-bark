<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FerrySlot extends Model
{
    protected $fillable = [
        'ferry_id',
        'slot_date',
        'start_time',
        'end_time',
        'capacity',
        'status',
    ];

    public function ferry(): BelongsTo
    {
        return $this->belongsTo(Ferry::class);
    }

    public function ferryBookings(): HasMany
    {
        return $this->hasMany(FerryBooking::class);
    }

    public function getBookedCount(): int
    {
        return $this->ferryBookings()->where('status', '!=', 'canceled')->count();
    }
}
