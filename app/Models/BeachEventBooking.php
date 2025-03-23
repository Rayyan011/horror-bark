<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class BeachEventBooking extends Model
{
    protected $fillable = [
        'user_id',
        'beach_event_id',
        'booking_date',  // Date of booking (should match event_date)
        'quantity',
        'total_price',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function beachEvent(): BelongsTo
    {
        return $this->belongsTo(BeachEvent::class);
    }

}
