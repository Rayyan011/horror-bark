<?php

namespace App\Filament\Widgets;

use App\Models\BeachEventBooking;
use App\Models\FerryBooking;
use App\Models\GameBooking;
use App\Models\HotelBooking;
use App\Models\Invoice;
use App\Models\RideBooking;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class AdminStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalBookings = $this->sumBookings(fn ($query) => $query->count());
        $pendingBookings = $this->sumBookings(fn ($query) => $query->where('status', 'pending')->count());

        $since = Carbon::now()->subDays(30);
        $revenueLast30Days = Invoice::query()
            ->where('issued_at', '>=', $since)
            ->sum('amount');

        $newUsersLast30Days = User::query()
            ->where('created_at', '>=', $since)
            ->count();

        return [
            Stat::make('Total Bookings', number_format($totalBookings))
                ->description('All booking types')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('primary'),
            Stat::make('Pending Bookings', number_format($pendingBookings))
                ->description('Needs attention')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make('Revenue (30 Days)', 'MVR ' . number_format($revenueLast30Days, 2))
                ->description('Invoices issued in last 30 days')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
            Stat::make('New Customers (30 Days)', number_format($newUsersLast30Days))
                ->description('Users created in last 30 days')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('info'),
        ];
    }

    private function sumBookings(callable $callback): int
    {
        $bookings = [
            HotelBooking::query(),
            FerryBooking::query(),
            RideBooking::query(),
            GameBooking::query(),
            BeachEventBooking::query(),
        ];

        $total = 0;
        foreach ($bookings as $query) {
            $total += (int) $callback($query);
        }

        return $total;
    }
}
