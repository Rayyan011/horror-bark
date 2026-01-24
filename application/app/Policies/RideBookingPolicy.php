<?php

namespace App\Policies;

use App\Models\RideBooking;
use App\Models\User;

class RideBookingPolicy
{
    public function view(User $user, RideBooking $rideBooking): bool
    {
        return $rideBooking->user_id === $user->id;
    }

    public function update(User $user, RideBooking $rideBooking): bool
    {
        return $rideBooking->user_id === $user->id;
    }
}
