<?php

namespace App\Filament\User\Widgets;

use App\Filament\Widgets\QuickActionsWidget;
use App\Models\FerryBooking;
use App\Models\GameBooking;
use App\Models\HotelBooking;
use App\Models\RideBooking;

class UserQuickActionsWidget extends QuickActionsWidget
{
    protected function getActions(): array
    {
        return [
            [
                'label' => 'Book a Hotel',
                'url' => url('/user/hotel-bookings/create'),
                'icon' => 'heroicon-m-building-office',
            ],
            [
                'label' => 'Book a Ferry',
                'url' => url('/user/ferry-bookings/create'),
                'icon' => 'heroicon-m-ticket',
            ],
            [
                'label' => 'Book a Ride',
                'url' => url('/user/ride-bookings/create'),
                'icon' => 'heroicon-m-truck',
            ],
            [
                'label' => 'Book a Game',
                'url' => url('/user/game-bookings/create'),
                'icon' => 'heroicon-m-sparkles',
            ],
        ];
    }

    protected function shouldShowEmptyState(): bool
    {
        return $this->userBookingsCount() === 0;
    }

    protected function getEmptyStateMessage(): ?string
    {
        return 'No bookings yet. Pick a hotel, ride, ferry, or game to get started.';
    }

    private function userBookingsCount(): int
    {
        $userId = auth()->id();

        return HotelBooking::query()->where('user_id', $userId)->count()
            + FerryBooking::query()->where('user_id', $userId)->count()
            + RideBooking::query()->where('user_id', $userId)->count()
            + GameBooking::query()->where('user_id', $userId)->count();
    }
}
