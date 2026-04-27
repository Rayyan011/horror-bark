<?php

namespace App\Services;

use App\Models\BeachEvent;
use App\Models\Ferry;
use App\Models\Game;
use App\Models\HotelBooking;
use App\Models\Ride;
use App\Models\User;
use Carbon\Carbon;

class IslandAccessService
{
    public const HORROR_ISLAND = 'Horror-Island';

    public const PICNIC_ISLAND = 'Picnic-Island';

    public const REQUIRED_STAY_ERROR = 'A confirmed hotel stay is required before booking this activity.';

    public function ferryRequiresHotel(Ferry $ferry): bool
    {
        return $this->isHorrorIsland($ferry->island?->type ?? self::HORROR_ISLAND);
    }

    public function rideRequiresHotel(Ride $ride): bool
    {
        return true;
    }

    public function gameRequiresHotel(Game $game): bool
    {
        return true;
    }

    public function beachEventRequiresHotel(BeachEvent $beachEvent): bool
    {
        return true;
    }

    public function hasConfirmedHotelStayAt(User $user, Carbon $activityAt): bool
    {
        return HotelBooking::query()
            ->where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->where('start_date', '<=', $activityAt)
            ->where('end_date', '>', $activityAt)
            ->exists();
    }

    public function hasConfirmedHotelStayForFerryAt(User $user, Carbon $ferryAt): bool
    {
        return HotelBooking::query()
            ->where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->where('start_date', '<=', $ferryAt)
            ->whereDate('end_date', '>=', $ferryAt->toDateString())
            ->exists();
    }

    private function isHorrorIsland(?string $type): bool
    {
        return $type === self::HORROR_ISLAND;
    }
}
