<?php

namespace App\Filament\Ride\Widgets;

use App\Filament\Widgets\QuickActionsWidget;
use App\Models\RideBooking;

class RideQuickActionsWidget extends QuickActionsWidget
{
    protected function getActions(): array
    {
        return [
            [
                'label' => 'Create Ride',
                'url' => url('/ride/rides/create'),
                'icon' => 'heroicon-m-truck',
            ],
            [
                'label' => 'View Bookings',
                'url' => url('/ride/ride-bookings'),
                'icon' => 'heroicon-m-ticket',
            ],
            [
                'label' => 'Create Booking',
                'url' => url('/ride/ride-bookings/create'),
                'icon' => 'heroicon-m-plus',
                'color' => 'secondary',
            ],
            [
                'label' => 'Reports',
                'url' => route('operator-reports.index', ['domain' => 'ride']),
                'icon' => 'heroicon-m-chart-bar',
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
        return 'No ride bookings yet. Create a ride and share your booking link.';
    }

    private function ownerBookingsCount(): int
    {
        return RideBooking::query()
            ->whereHas('ride', fn ($query) => $query->where('user_id', auth()->id()))
            ->count();
    }
}
