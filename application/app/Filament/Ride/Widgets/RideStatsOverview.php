<?php

namespace App\Filament\Ride\Widgets;

use App\Models\Ride;
use App\Models\RideBooking;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class RideStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $ownerId = auth()->id();

        $rideCount = Ride::query()
            ->where('user_id', $ownerId)
            ->count();

        $bookingsQuery = RideBooking::query()
            ->whereHas('ride', fn ($query) => $query->where('user_id', $ownerId));

        $pendingBookings = (clone $bookingsQuery)->where('status', 'pending')->count();

        $since = Carbon::now()->subDays(30);
        $recentBookings = (clone $bookingsQuery)->where('created_at', '>=', $since)->count();
        $revenueLast30Days = (clone $bookingsQuery)
            ->where('created_at', '>=', $since)
            ->sum('total_price');

        return [
            Stat::make('My Rides', number_format($rideCount))
                ->description('Rides you manage')
                ->descriptionIcon('heroicon-m-truck')
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
