<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class RideBooking extends Model
{
    protected $fillable = [
        'user_id',
        'ride_id',
        'booking_time',  // datetime field: allowed only at 9:00 or 17:00
        'quantity',
        'total_price',
        'status',
        'reminder_sent_at',
    ];

    protected $casts = [
        'booking_time' => 'datetime',
        'reminder_sent_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ride(): BelongsTo
    {
        return $this->belongsTo(Ride::class);
    }

    public function invoice(): MorphOne
    {
        return $this->morphOne(Invoice::class, 'invoiceable');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
