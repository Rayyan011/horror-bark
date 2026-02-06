<?php

namespace App\Filament\User\Widgets;

use App\Models\FerryBooking;
use App\Models\GameBooking;
use App\Models\HotelBooking;
use App\Models\RideBooking;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class UserNext7DaysOverview extends StatsOverviewWidget
{
    protected static ?int $sort = -1;

    protected ?string $heading = 'Next 7 Days';

    protected function getStats(): array
    {
        $userId = auth()->id();
        $todayStart = Carbon::now()->startOfDay();
        $tomorrowStart = $todayStart->copy()->addDay();
        $weekEnd = $todayStart->copy()->addDays(7);

        $todayBookings = $this->countBookingsInRange($userId, $todayStart, $tomorrowStart);
        $nextWeekBookings = $this->countBookingsInRange($userId, $todayStart, $weekEnd);
        $pendingApprovals = $this->countBookingsInRange($userId, $todayStart, $weekEnd, status: 'pending');
        $cancellations = $this->countBookingsInRange($userId, $todayStart, $weekEnd, status: 'canceled');

        return [
            Stat::make('Bookings Today', number_format($todayBookings))
                ->description('Today’s schedule')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary'),
            Stat::make('Bookings (7 Days)', number_format($nextWeekBookings))
                ->description('Upcoming bookings')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),
            Stat::make('Pending Approvals', number_format($pendingApprovals))
                ->description('Awaiting confirmation')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make('Cancellations', number_format($cancellations))
                ->description('Next 7 days')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }

    private function countBookingsInRange(int $userId, Carbon $start, Carbon $end, ?string $status = null): int
    {
        $hotelQuery = HotelBooking::query()
            ->where('user_id', $userId)
            ->where('start_date', '>=', $start)
            ->where('start_date', '<', $end);

        $ferryQuery = FerryBooking::query()
            ->where('user_id', $userId)
            ->where('booking_time', '>=', $start)
            ->where('booking_time', '<', $end);

        $rideQuery = RideBooking::query()
            ->where('user_id', $userId)
            ->where('booking_time', '>=', $start)
            ->where('booking_time', '<', $end);

        $gameQuery = GameBooking::query()
            ->where('user_id', $userId)
            ->where('booking_time', '>=', $start)
            ->where('booking_time', '<', $end);

        if ($status !== null) {
            $hotelQuery->where('status', $status);
            $ferryQuery->where('status', $status);
            $rideQuery->where('status', $status);
            $gameQuery->where('status', $status);
        } else {
            $hotelQuery->where('status', '!=', 'canceled');
            $ferryQuery->where('status', '!=', 'canceled');
            $rideQuery->where('status', '!=', 'canceled');
            $gameQuery->where('status', '!=', 'canceled');
        }

        return $hotelQuery->count()
            + $ferryQuery->count()
            + $rideQuery->count()
            + $gameQuery->count();
    }
}
