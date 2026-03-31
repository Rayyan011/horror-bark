<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class BeachEventBooking extends Model
{
    protected $fillable = [
        'user_id',
        'beach_event_id',
        'booking_date',  // Date of booking (should match event_date)
        'booking_time',
        'quantity',
        'total_price',
        'status',
        'reminder_sent_at',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'booking_time' => 'datetime',
        'reminder_sent_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function beachEvent(): BelongsTo
    {
        return $this->belongsTo(BeachEvent::class);
    }

    public function invoice(): MorphOne
    {
        return $this->morphOne(Invoice::class, 'invoiceable');
    }
}
