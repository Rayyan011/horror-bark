<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class RideBooking extends Model
{
    protected $fillable = [
        'user_id',
        'ride_id',
        'booking_time',  // datetime field: allowed only at 9:00 or 17:00
        'quantity',
        'total_price',
        'status'
    ];

    public function user(): BelongsTo 
    {
        return $this->belongsTo(User::class);
    }

    public function ride(): BelongsTo 
    {
        return $this->belongsTo(Ride::class);
    }

    public function isPending(): bool 
    {
        return $this->status === 'pending';
    }

}
