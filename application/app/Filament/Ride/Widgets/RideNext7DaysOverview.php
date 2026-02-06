<?php

namespace App\Filament\Ride\Widgets;

use App\Models\RideBooking;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class RideNext7DaysOverview extends StatsOverviewWidget
{
    protected static ?int $sort = -1;

    protected ?string $heading = 'Next 7 Days';

    protected function getStats(): array
    {
        $ownerId = auth()->id();
        $todayStart = Carbon::now()->startOfDay();
        $tomorrowStart = $todayStart->copy()->addDay();
        $weekEnd = $todayStart->copy()->addDays(7);

        $baseQuery = RideBooking::query()
            ->whereHas('ride', fn ($query) => $query->where('user_id', $ownerId));

        $todayBookings = (clone $baseQuery)
            ->where('status', '!=', 'canceled')
            ->where('booking_time', '>=', $todayStart)
            ->where('booking_time', '<', $tomorrowStart)
            ->count();

        $nextWeekBookings = (clone $baseQuery)
            ->where('status', '!=', 'canceled')
            ->where('booking_time', '>=', $todayStart)
            ->where('booking_time', '<', $weekEnd)
            ->count();

        $pendingApprovals = (clone $baseQuery)
            ->where('status', 'pending')
            ->where('booking_time', '>=', $todayStart)
            ->where('booking_time', '<', $weekEnd)
            ->count();

        $cancellations = (clone $baseQuery)
            ->where('status', 'canceled')
            ->where('booking_time', '>=', $todayStart)
            ->where('booking_time', '<', $weekEnd)
            ->count();

        return [
            Stat::make('Bookings Today', number_format($todayBookings))
                ->description('Today’s slots')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary'),
            Stat::make('Bookings (7 Days)', number_format($nextWeekBookings))
                ->description('Upcoming slots')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),
            Stat::make('Pending Approvals', number_format($pendingApprovals))
                ->description('Needs confirmation')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make('Cancellations', number_format($cancellations))
                ->description('Next 7 days')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}
