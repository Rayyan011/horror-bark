<?php

namespace App\Filament\Widgets;

use App\Models\BeachEventBooking;
use App\Models\FerryBooking;
use App\Models\GameBooking;
use App\Models\HotelBooking;
use App\Models\Invoice;
use App\Models\RideBooking;
use App\Models\User;
use App\Filament\Widgets\PeriodStatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class AdminStatsOverview extends PeriodStatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        [$start, $end] = $this->getPeriodRange();
        [$prevStart, $prevEnd] = $this->getPreviousPeriodRange();

        $totalBookings = $this->countBookingsBetween($start, $end);
        $prevTotalBookings = $this->countBookingsBetween($prevStart, $prevEnd);

        $pendingBookings = $this->countBookingsBetween($start, $end, status: 'pending');
        $prevPendingBookings = $this->countBookingsBetween($prevStart, $prevEnd, status: 'pending');

        $revenue = Invoice::query()
            ->where('issued_at', '>=', $start)
            ->where('issued_at', '<', $end)
            ->sum('amount');
        $prevRevenue = Invoice::query()
            ->where('issued_at', '>=', $prevStart)
            ->where('issued_at', '<', $prevEnd)
            ->sum('amount');

        $newUsers = User::query()
            ->where('created_at', '>=', $start)
            ->where('created_at', '<', $end)
            ->count();
        $prevNewUsers = User::query()
            ->where('created_at', '>=', $prevStart)
            ->where('created_at', '<', $prevEnd)
            ->count();

        [$totalDesc, $totalIcon, $totalColor] = $this->buildDescriptionWithDelta('All booking types', $totalBookings, $prevTotalBookings);
        [$pendingDesc, $pendingIcon, $pendingColor] = $this->buildDescriptionWithDelta('Needs attention', $pendingBookings, $prevPendingBookings);
        [$revenueDesc, $revenueIcon, $revenueColor] = $this->buildDescriptionWithDelta('Invoices issued', $revenue, $prevRevenue);
        [$usersDesc, $usersIcon, $usersColor] = $this->buildDescriptionWithDelta('Users created', $newUsers, $prevNewUsers);

        return [
            Stat::make('Bookings', number_format($totalBookings))
                ->description($totalDesc)
                ->descriptionIcon($totalIcon)
                ->descriptionColor($totalColor)
                ->color('primary'),
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
            Stat::make('New Customers', number_format($newUsers))
                ->description($usersDesc)
                ->descriptionIcon($usersIcon)
                ->descriptionColor($usersColor)
                ->color('info'),
        ];
    }

    private function countBookingsBetween(Carbon $start, Carbon $end, ?string $status = null): int
    {
        $total = 0;

        $hotelQuery = HotelBooking::query()
            ->where('status', '!=', 'canceled')
            ->where('start_date', '>=', $start)
            ->where('start_date', '<', $end);

        $ferryQuery = FerryBooking::query()
            ->where('status', '!=', 'canceled')
            ->where('booking_time', '>=', $start)
            ->where('booking_time', '<', $end);

        $rideQuery = RideBooking::query()
            ->where('status', '!=', 'canceled')
            ->where('booking_time', '>=', $start)
            ->where('booking_time', '<', $end);

        $gameQuery = GameBooking::query()
            ->where('status', '!=', 'canceled')
            ->where('booking_time', '>=', $start)
            ->where('booking_time', '<', $end);

        $beachEventQuery = BeachEventBooking::query()
            ->where('status', '!=', 'canceled')
            ->where('booking_date', '>=', $start->toDateString())
            ->where('booking_date', '<', $end->toDateString());

        if ($status !== null) {
            $hotelQuery->where('status', $status);
            $ferryQuery->where('status', $status);
            $rideQuery->where('status', $status);
            $gameQuery->where('status', $status);
            $beachEventQuery->where('status', $status);
        }

        $total += $hotelQuery->count();
        $total += $ferryQuery->count();
        $total += $rideQuery->count();
        $total += $gameQuery->count();
        $total += $beachEventQuery->count();

        return $total;
    }
}
