<?php

namespace App\Filament\Ferry\Widgets;

use App\Filament\Widgets\Concerns\HasDashboardDateRange;
use App\Models\FerryBooking;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FerryNext7DaysOverview extends StatsOverviewWidget
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

        $baseQuery = FerryBooking::query()
            ->whereHas('ferry', fn ($query) => $query->where('user_id', $ownerId));

        $firstDayBookings = (clone $baseQuery)
            ->where('status', '!=', 'canceled')
            ->where('booking_time', '>=', $firstDayStart)
            ->where('booking_time', '<', $firstDayEnd)
            ->count();

        $rangeBookings = (clone $baseQuery)
            ->where('status', '!=', 'canceled')
            ->where('booking_time', '>=', $rangeStart)
            ->where('booking_time', '<', $rangeEnd)
            ->count();

        $pendingApprovals = (clone $baseQuery)
            ->where('status', 'pending')
            ->where('booking_time', '>=', $rangeStart)
            ->where('booking_time', '<', $rangeEnd)
            ->count();

        $cancellations = (clone $baseQuery)
            ->where('status', 'canceled')
            ->where('booking_time', '>=', $rangeStart)
            ->where('booking_time', '<', $rangeEnd)
            ->count();

        return [
            Stat::make('Departures First Day', number_format($firstDayBookings))
                ->description($firstDayStart->format('M j, Y'))
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary'),
            Stat::make('Departures In Range', number_format($rangeBookings))
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
