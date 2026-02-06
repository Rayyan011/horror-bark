<?php

namespace App\Filament\Hotel\Widgets;

use App\Models\HotelBooking;
use App\Models\Room;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class HotelStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalBookings = HotelBooking::query()->count();
        $pendingBookings = HotelBooking::query()->where('status', 'pending')->count();
        $availableRooms = Room::query()->where('status', 'available')->count();

        $since = Carbon::now()->subDays(30);
        $revenueLast30Days = HotelBooking::query()
            ->where('created_at', '>=', $since)
            ->sum('total_price');

        return [
            Stat::make('Hotel Bookings', number_format($totalBookings))
                ->description('All hotel bookings')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('primary'),
            Stat::make('Pending Bookings', number_format($pendingBookings))
                ->description('Needs attention')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make('Revenue (30 Days)', 'MVR ' . number_format($revenueLast30Days, 2))
                ->description('Bookings created in last 30 days')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
            Stat::make('Rooms Available', number_format($availableRooms))
                ->description('Marked available')
                ->descriptionIcon('heroicon-m-home')
                ->color('info'),
        ];
    }
}
