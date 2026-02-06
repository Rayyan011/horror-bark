<?php

namespace App\Filament\Ferry\Widgets;

use App\Models\Ferry;
use App\Models\FerryBooking;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class FerryStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $ownerId = auth()->id();

        $ferryCount = Ferry::query()
            ->where('user_id', $ownerId)
            ->count();

        $bookingsQuery = FerryBooking::query()
            ->whereHas('ferry', fn ($query) => $query->where('user_id', $ownerId));

        $pendingBookings = (clone $bookingsQuery)->where('status', 'pending')->count();

        $since = Carbon::now()->subDays(30);
        $recentBookings = (clone $bookingsQuery)->where('created_at', '>=', $since)->count();
        $revenueLast30Days = (clone $bookingsQuery)
            ->where('created_at', '>=', $since)
            ->sum('total_price');

        return [
            Stat::make('My Ferries', number_format($ferryCount))
                ->description('Active ferries you manage')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('primary'),
            Stat::make('Bookings (30 Days)', number_format($recentBookings))
                ->description('Recent booking volume')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('info'),
            Stat::make('Pending Bookings', number_format($pendingBookings))
                ->description('Needs confirmation')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make('Revenue (30 Days)', 'MVR ' . number_format($revenueLast30Days, 2))
                ->description('Bookings created in last 30 days')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
}
