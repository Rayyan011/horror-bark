<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class HotelBooking extends Model
{
    protected $fillable = [
        'user_id',
        'room_id',
        'start_date',
        'end_date',
        'total_price',
        'quantity',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }


}
