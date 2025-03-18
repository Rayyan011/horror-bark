<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class FerryBooking extends Model
{
    protected $fillable = [
        'user_id',
        'ferry_id',
        'booking_time',   // Datetime: must start on the hour between 9:00 and 16:00.
        'quantity',
        'total_price',
        'status',
    ];

    public function user(): BelongsTo 
    {
        return $this->belongsTo(User::class);
    }

    public function ferry(): BelongsTo 
    {
        return $this->belongsTo(Ferry::class);
    }

    public function isPending(): bool 
    {
        return $this->status === 'pending';
    }

}
