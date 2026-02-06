<?php

namespace App\Filament\User\Widgets;

use App\Models\FerryBooking;
use App\Models\GameBooking;
use App\Models\HotelBooking;
use App\Models\RideBooking;
use App\Filament\Widgets\PeriodStatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class UserStatsOverview extends PeriodStatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $userId = auth()->id();

        [$start, $end] = $this->getPeriodRange();
        [$prevStart, $prevEnd] = $this->getPreviousPeriodRange();

        $totalBookings = $this->countBookingsInPeriod($userId, $start, $end);
        $prevTotalBookings = $this->countBookingsInPeriod($userId, $prevStart, $prevEnd);

        $pendingBookings = $this->countBookingsInPeriod($userId, $start, $end, status: 'pending');
        $prevPendingBookings = $this->countBookingsInPeriod($userId, $prevStart, $prevEnd, status: 'pending');

        $canceledBookings = $this->countBookingsInPeriod($userId, $start, $end, status: 'canceled');
        $prevCanceledBookings = $this->countBookingsInPeriod($userId, $prevStart, $prevEnd, status: 'canceled');

        $spend = $this->sumSpendInPeriod($userId, $start, $end);
        $prevSpend = $this->sumSpendInPeriod($userId, $prevStart, $prevEnd);

        [$bookingsDesc, $bookingsIcon, $bookingsColor] = $this->buildDescriptionWithDelta('All booking types', $totalBookings, $prevTotalBookings);
        [$pendingDesc, $pendingIcon, $pendingColor] = $this->buildDescriptionWithDelta('Awaiting confirmation', $pendingBookings, $prevPendingBookings);
        [$canceledDesc, $canceledIcon, $canceledColor] = $this->buildDescriptionWithDelta('Canceled in period', $canceledBookings, $prevCanceledBookings);
        [$spendDesc, $spendIcon, $spendColor] = $this->buildDescriptionWithDelta('Bookings in period', $spend, $prevSpend);

        return [
            Stat::make('My Bookings', number_format($totalBookings))
                ->description($bookingsDesc)
                ->descriptionIcon($bookingsIcon)
                ->descriptionColor($bookingsColor)
                ->color('primary'),
            Stat::make('Pending Bookings', number_format($pendingBookings))
                ->description($pendingDesc)
                ->descriptionIcon($pendingIcon)
                ->descriptionColor($pendingColor)
                ->color('warning'),
            Stat::make('Canceled Bookings', number_format($canceledBookings))
                ->description($canceledDesc)
                ->descriptionIcon($canceledIcon)
                ->descriptionColor($canceledColor)
                ->color('danger'),
            Stat::make('Spend', 'MVR ' . number_format($spend, 2))
                ->description($spendDesc)
                ->descriptionIcon($spendIcon)
                ->descriptionColor($spendColor)
                ->color('success'),
        ];
    }

    private function countBookingsInPeriod(int $userId, Carbon $start, Carbon $end, ?string $status = null): int
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

    private function sumSpendInPeriod(int $userId, Carbon $start, Carbon $end): float
    {
        $hotelSpend = HotelBooking::query()
            ->where('user_id', $userId)
            ->where('status', '!=', 'canceled')
            ->where('start_date', '>=', $start)
            ->where('start_date', '<', $end)
            ->sum('total_price');

        $ferrySpend = FerryBooking::query()
            ->where('user_id', $userId)
            ->where('status', '!=', 'canceled')
            ->where('booking_time', '>=', $start)
            ->where('booking_time', '<', $end)
            ->sum('total_price');

        $rideSpend = RideBooking::query()
            ->where('user_id', $userId)
            ->where('status', '!=', 'canceled')
            ->where('booking_time', '>=', $start)
            ->where('booking_time', '<', $end)
            ->sum('total_price');

        $gameSpend = GameBooking::query()
            ->where('user_id', $userId)
            ->where('status', '!=', 'canceled')
            ->where('booking_time', '>=', $start)
            ->where('booking_time', '<', $end)
            ->sum('total_price');

        return (float) ($hotelSpend + $ferrySpend + $rideSpend + $gameSpend);
    }
}
