<?php

namespace App\Filament\Hotel\Widgets;

use App\Models\HotelBooking;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class HotelNext7DaysOverview extends StatsOverviewWidget
{
    protected static ?int $sort = -1;

    protected ?string $heading = 'Next 7 Days';

    protected function getStats(): array
    {
        $ownerId = auth()->id();
        $todayStart = Carbon::now()->startOfDay();
        $tomorrowStart = $todayStart->copy()->addDay();
        $weekEnd = $todayStart->copy()->addDays(7);

        $baseQuery = HotelBooking::query()
            ->whereHas('room.hotel', fn ($query) => $query->where('user_id', $ownerId));

        $todayArrivals = (clone $baseQuery)
            ->where('status', '!=', 'canceled')
            ->where('start_date', '>=', $todayStart)
            ->where('start_date', '<', $tomorrowStart)
            ->count();

        $nextWeekArrivals = (clone $baseQuery)
            ->where('status', '!=', 'canceled')
            ->where('start_date', '>=', $todayStart)
            ->where('start_date', '<', $weekEnd)
            ->count();

        $pendingApprovals = (clone $baseQuery)
            ->where('status', 'pending')
            ->where('start_date', '>=', $todayStart)
            ->where('start_date', '<', $weekEnd)
            ->count();

        $cancellations = (clone $baseQuery)
            ->where('status', 'canceled')
            ->where('start_date', '>=', $todayStart)
            ->where('start_date', '<', $weekEnd)
            ->count();

        return [
            Stat::make('Arrivals Today', number_format($todayArrivals))
                ->description('Check-ins today')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary'),
            Stat::make('Arrivals (7 Days)', number_format($nextWeekArrivals))
                ->description('Upcoming check-ins')
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
