<?php

namespace App\Policies;

use App\Models\FerryBooking;
use App\Models\User;

class FerryBookingPolicy
{
    public function view(User $user, FerryBooking $ferryBooking): bool
    {
        return $ferryBooking->user_id === $user->id;
    }

    public function update(User $user, FerryBooking $ferryBooking): bool
    {
        return $ferryBooking->user_id === $user->id;
    }
}
