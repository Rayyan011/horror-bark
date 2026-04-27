<?php

namespace App\Filament\Hotel\Widgets;

use App\Filament\Widgets\Concerns\HasDashboardDateRange;
use App\Models\HotelBooking;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class HotelNext7DaysOverview extends StatsOverviewWidget
{
    use HasDashboardDateRange;

    protected static ?int $sort = -1;

    protected ?string $heading = 'Selected Date Range';

    protected function getStats(): array
    {
        $ownerId = auth()->id();
        [$rangeStart, $rangeEnd] = $this->getDashboardDateRange();
        [$firstDayStart, $firstDayEnd] = $this->getFirstDashboardDateRange();
        $rangeLabel = $this->getDashboardDateRangeLabel();

        $baseQuery = HotelBooking::query()
            ->whereHas('room.hotel', fn ($query) => $query->where('user_id', $ownerId));

        $firstDayArrivals = (clone $baseQuery)
            ->where('status', '!=', 'canceled')
            ->where('start_date', '>=', $firstDayStart)
            ->where('start_date', '<', $firstDayEnd)
            ->count();

        $rangeArrivals = (clone $baseQuery)
            ->where('status', '!=', 'canceled')
            ->where('start_date', '>=', $rangeStart)
            ->where('start_date', '<', $rangeEnd)
            ->count();

        $pendingApprovals = (clone $baseQuery)
            ->where('status', 'pending')
            ->where('start_date', '>=', $rangeStart)
            ->where('start_date', '<', $rangeEnd)
            ->count();

        $cancellations = (clone $baseQuery)
            ->where('status', 'canceled')
            ->where('start_date', '>=', $rangeStart)
            ->where('start_date', '<', $rangeEnd)
            ->count();

        return [
            Stat::make('Arrivals First Day', number_format($firstDayArrivals))
                ->description($firstDayStart->format('M j, Y'))
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary'),
            Stat::make('Arrivals In Range', number_format($rangeArrivals))
                ->description($rangeLabel)
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),
            Stat::make('Pending Approvals', number_format($pendingApprovals))
                ->description('Needs confirmation')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make('Cancellations', number_format($cancellations))
                ->description($rangeLabel)
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}
