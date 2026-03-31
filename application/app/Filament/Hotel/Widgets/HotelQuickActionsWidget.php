<?php

namespace App\Filament\Hotel\Widgets;

use App\Filament\Widgets\QuickActionsWidget;
use App\Models\HotelBooking;

class HotelQuickActionsWidget extends QuickActionsWidget
{
    protected function getActions(): array
    {
        return [
            [
                'label' => 'View Bookings',
                'url' => url('/hotel/hotel-bookings'),
                'icon' => 'heroicon-m-ticket',
            ],
            [
                'label' => 'Create Booking',
                'url' => url('/hotel/hotel-bookings/create'),
                'icon' => 'heroicon-m-plus',
                'color' => 'secondary',
            ],
            [
                'label' => 'Manage Hotels',
                'url' => url('/hotel/hotels'),
                'icon' => 'heroicon-m-building-office',
            ],
            [
                'label' => 'Reports',
                'url' => route('operator-reports.index', ['domain' => 'hotel']),
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
        return 'No hotel bookings yet. Add rooms and share your booking link.';
    }

    private function ownerBookingsCount(): int
    {
        return HotelBooking::query()
            ->whereHas('room.hotel', fn ($query) => $query->where('user_id', auth()->id()))
            ->count();
    }
}
