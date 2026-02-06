<?php

namespace App\Filament\User\Widgets;

use App\Models\FerryBooking;
use App\Models\GameBooking;
use App\Models\HotelBooking;
use App\Models\RideBooking;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class UserStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $userId = auth()->id();

        $totalBookings = $this->sumBookings(fn ($query) => $query->count(), $userId);
        $pendingBookings = $this->sumBookings(fn ($query) => $query->where('status', 'pending')->count(), $userId);
        $upcomingBookings = $this->countUpcomingBookings($userId);

        $since = Carbon::now()->subDays(30);
        $spendLast30Days = $this->sumBookings(
            fn ($query) => $query->where('created_at', '>=', $since)->sum('total_price'),
            $userId
        );

        return [
            Stat::make('My Bookings', number_format($totalBookings))
                ->description('All booking types')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('primary'),
            Stat::make('Pending Bookings', number_format($pendingBookings))
                ->description('Awaiting confirmation')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make('Upcoming Bookings', number_format($upcomingBookings))
                ->description('Future dates')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),
            Stat::make('Spend (30 Days)', 'MVR ' . number_format($spendLast30Days, 2))
                ->description('Bookings created in last 30 days')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }

    private function sumBookings(callable $callback, int $userId): int|float
    {
        $bookings = [
            HotelBooking::query()->where('user_id', $userId),
            FerryBooking::query()->where('user_id', $userId),
            RideBooking::query()->where('user_id', $userId),
            GameBooking::query()->where('user_id', $userId),
        ];

        $total = 0;
        foreach ($bookings as $query) {
            $total += $callback($query);
        }

        return $total;
    }

    private function countUpcomingBookings(int $userId): int
    {
        $now = Carbon::now();

        $hotelCount = HotelBooking::query()
            ->where('user_id', $userId)
            ->where('status', '!=', 'cancelled')
            ->where('start_date', '>=', $now)
            ->count();

        $ferryCount = FerryBooking::query()
            ->where('user_id', $userId)
            ->where('status', '!=', 'cancelled')
            ->where('booking_time', '>=', $now)
            ->count();

        $rideCount = RideBooking::query()
            ->where('user_id', $userId)
            ->where('status', '!=', 'cancelled')
            ->where('booking_time', '>=', $now)
            ->count();

        $gameCount = GameBooking::query()
            ->where('user_id', $userId)
            ->where('status', '!=', 'cancelled')
            ->where('booking_time', '>=', $now)
            ->count();

        return $hotelCount + $ferryCount + $rideCount + $gameCount;
    }
}
