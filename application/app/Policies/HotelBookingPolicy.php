<?php

namespace App\Policies;

use App\Models\HotelBooking;
use App\Models\User;

class HotelBookingPolicy
{
    public function view(User $user, HotelBooking $hotelBooking): bool
    {
        return $hotelBooking->user_id === $user->id;
    }

    public function update(User $user, HotelBooking $hotelBooking): bool
    {
        return $hotelBooking->user_id === $user->id;
    }
}
