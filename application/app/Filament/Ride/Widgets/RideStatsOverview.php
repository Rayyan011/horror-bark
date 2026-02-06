<?php

namespace App\Filament\Ride\Widgets;

use App\Models\RideBooking;
use App\Filament\Widgets\PeriodStatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RideStatsOverview extends PeriodStatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $ownerId = auth()->id();

        [$start, $end] = $this->getPeriodRange();
        [$prevStart, $prevEnd] = $this->getPreviousPeriodRange();

        $bookingsQuery = RideBooking::query()
            ->whereHas('ride', fn ($query) => $query->where('user_id', $ownerId));

        $bookings = (clone $bookingsQuery)
            ->where('status', '!=', 'canceled')
            ->where('booking_time', '>=', $start)
            ->where('booking_time', '<', $end);
        $prevBookings = (clone $bookingsQuery)
            ->where('status', '!=', 'canceled')
            ->where('booking_time', '>=', $prevStart)
            ->where('booking_time', '<', $prevEnd);

        $bookingCount = $bookings->count();
        $prevBookingCount = $prevBookings->count();

        $riderCount = (clone $bookings)->sum('quantity');
        $prevRiderCount = (clone $prevBookings)->sum('quantity');

        $pendingBookings = (clone $bookingsQuery)
            ->where('status', 'pending')
            ->where('booking_time', '>=', $start)
            ->where('booking_time', '<', $end)
            ->count();
        $prevPendingBookings = (clone $bookingsQuery)
            ->where('status', 'pending')
            ->where('booking_time', '>=', $prevStart)
            ->where('booking_time', '<', $prevEnd)
            ->count();

        $revenue = (clone $bookings)->sum('total_price');
        $prevRevenue = (clone $prevBookings)->sum('total_price');

        [$bookingDesc, $bookingIcon, $bookingColor] = $this->buildDescriptionWithDelta('Bookings in period', $bookingCount, $prevBookingCount);
        [$riderDesc, $riderIcon, $riderColor] = $this->buildDescriptionWithDelta('Tickets sold', $riderCount, $prevRiderCount);
        [$pendingDesc, $pendingIcon, $pendingColor] = $this->buildDescriptionWithDelta('Needs confirmation', $pendingBookings, $prevPendingBookings);
        [$revenueDesc, $revenueIcon, $revenueColor] = $this->buildDescriptionWithDelta('Bookings in period', $revenue, $prevRevenue);

        return [
            Stat::make('Bookings', number_format($bookingCount))
                ->description($bookingDesc)
                ->descriptionIcon($bookingIcon)
                ->descriptionColor($bookingColor)
                ->color('primary'),
            Stat::make('Riders', number_format($riderCount))
                ->description($riderDesc)
                ->descriptionIcon($riderIcon)
                ->descriptionColor($riderColor)
                ->color('info'),
            Stat::make('Pending Bookings', number_format($pendingBookings))
                ->description($pendingDesc)
                ->descriptionIcon($pendingIcon)
                ->descriptionColor($pendingColor)
                ->color('warning'),
            Stat::make('Revenue', 'MVR ' . number_format($revenue, 2))
                ->description($revenueDesc)
                ->descriptionIcon($revenueIcon)
                ->descriptionColor($revenueColor)
                ->color('success'),
        ];
    }
}
