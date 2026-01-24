<?php

namespace App\Policies;

use App\Models\BeachEventBooking;
use App\Models\User;

class BeachEventBookingPolicy
{
    public function view(User $user, BeachEventBooking $beachEventBooking): bool
    {
        return $beachEventBooking->user_id === $user->id;
    }

    public function update(User $user, BeachEventBooking $beachEventBooking): bool
    {
        return $beachEventBooking->user_id === $user->id;
    }
}
