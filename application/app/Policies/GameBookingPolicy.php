<?php

namespace App\Policies;

use App\Models\GameBooking;
use App\Models\User;

class GameBookingPolicy
{
    public function view(User $user, GameBooking $gameBooking): bool
    {
        return $gameBooking->user_id === $user->id;
    }

    public function update(User $user, GameBooking $gameBooking): bool
    {
        return $gameBooking->user_id === $user->id;
    }
}
