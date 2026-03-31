<?php

namespace App\Filament\Widgets;

use App\Models\BeachEventBooking;
use App\Models\FerryBooking;
use App\Models\GameBooking;
use App\Models\HotelBooking;
use App\Models\RideBooking;

class AdminQuickActionsWidget extends QuickActionsWidget
{
    protected function getActions(): array
    {
        return [
            [
                'label' => 'Create Hotel',
                'url' => url('/admin/hotels/create'),
                'icon' => 'heroicon-m-building-office',
            ],
            [
                'label' => 'Create Ferry',
                'url' => url('/admin/ferries/create'),
                'icon' => 'heroicon-m-ticket',
            ],
            [
                'label' => 'Create Ride',
                'url' => url('/admin/rides/create'),
                'icon' => 'heroicon-m-truck',
            ],
            [
                'label' => 'Review Bookings',
                'url' => url('/admin/hotel-bookings'),
                'icon' => 'heroicon-m-ticket',
                'color' => 'secondary',
            ],
            [
                'label' => 'Reports',
                'url' => route('admin-reports.index'),
                'icon' => 'heroicon-m-chart-bar',
                'color' => 'secondary',
            ],
        ];
    }

    protected function shouldShowEmptyState(): bool
    {
        return $this->totalBookings() === 0;
    }

    protected function getEmptyStateMessage(): ?string
    {
        return 'No bookings yet. Create your first listings or bookings to get started.';
    }

    private function totalBookings(): int
    {
        return HotelBooking::query()->count()
            + FerryBooking::query()->count()
            + RideBooking::query()->count()
            + GameBooking::query()->count()
            + BeachEventBooking::query()->count();
    }
}
