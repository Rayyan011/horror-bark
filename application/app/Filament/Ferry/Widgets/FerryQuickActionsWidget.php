<?php

namespace App\Filament\Ferry\Widgets;

use App\Filament\Widgets\QuickActionsWidget;
use App\Models\FerryBooking;

class FerryQuickActionsWidget extends QuickActionsWidget
{
    protected function getActions(): array
    {
        return [
            [
                'label' => 'Create Ferry',
                'url' => url('/ferry/ferries/create'),
                'icon' => 'heroicon-m-ticket',
            ],
            [
                'label' => 'View Bookings',
                'url' => url('/ferry/ferry-bookings'),
                'icon' => 'heroicon-m-ticket',
            ],
            [
                'label' => 'Create Booking',
                'url' => url('/ferry/ferry-bookings/create'),
                'icon' => 'heroicon-m-plus',
                'color' => 'secondary',
            ],
            [
                'label' => 'Passenger Reports',
                'url' => route('ferry-reports.index'),
                'icon' => 'heroicon-m-document-chart-bar',
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
        return 'No ferry bookings yet. Add your ferry schedules to get started.';
    }

    private function ownerBookingsCount(): int
    {
        return FerryBooking::query()
            ->whereHas('ferry', fn ($query) => $query->where('user_id', auth()->id()))
            ->count();
    }
}
