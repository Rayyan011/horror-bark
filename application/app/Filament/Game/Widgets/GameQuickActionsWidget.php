<?php

namespace App\Filament\Game\Widgets;

use App\Filament\Widgets\QuickActionsWidget;
use App\Models\GameBooking;

class GameQuickActionsWidget extends QuickActionsWidget
{
    protected function getActions(): array
    {
        return [
            [
                'label' => 'Create Game',
                'url' => url('/game/games/create'),
                'icon' => 'heroicon-m-sparkles',
            ],
            [
                'label' => 'View Bookings',
                'url' => url('/game/game-bookings'),
                'icon' => 'heroicon-m-ticket',
            ],
            [
                'label' => 'Create Booking',
                'url' => url('/game/game-bookings/create'),
                'icon' => 'heroicon-m-plus',
                'color' => 'secondary',
            ],
        ];
    }

    protected function shouldShowEmptyState(): bool
    {
        return $this->ownerBookingsCount() === 0;
    }

    protected function getEmptyStateMessage(): ?string
    {
        return 'No game bookings yet. Create a game and start sharing it.';
    }

    private function ownerBookingsCount(): int
    {
        return GameBooking::query()
            ->whereHas('game', fn ($query) => $query->where('user_id', auth()->id()))
            ->count();
    }
}
