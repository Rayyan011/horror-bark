<?php

namespace App\Filament\Hotel\Widgets;

use App\Models\HotelBooking;
use App\Models\Room;
use App\Filament\Widgets\PeriodStatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class HotelStatsOverview extends PeriodStatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        [$start, $end] = $this->getPeriodRange();
        [$prevStart, $prevEnd] = $this->getPreviousPeriodRange();

        $bookedCapacityNights = $this->calculateBookedCapacityNights($start, $end);
        $prevBookedCapacityNights = $this->calculateBookedCapacityNights($prevStart, $prevEnd);

        $occupancyRate = $this->calculateOccupancyRate($bookedCapacityNights, $start, $end);
        $prevOccupancyRate = $this->calculateOccupancyRate($prevBookedCapacityNights, $prevStart, $prevEnd);

        $pendingBookings = $this->countPendingBookings($start, $end);
        $prevPendingBookings = $this->countPendingBookings($prevStart, $prevEnd);

        $revenue = $this->sumRevenue($start, $end);
        $prevRevenue = $this->sumRevenue($prevStart, $prevEnd);

        [$bookedDesc, $bookedIcon, $bookedColor] = $this->buildDescriptionWithDelta('Capacity-nights booked', $bookedCapacityNights, $prevBookedCapacityNights);
        [$occupancyDesc, $occupancyIcon, $occupancyColor] = $this->buildDescriptionWithDelta('Occupancy rate', $occupancyRate, $prevOccupancyRate);
        [$pendingDesc, $pendingIcon, $pendingColor] = $this->buildDescriptionWithDelta('Needs attention', $pendingBookings, $prevPendingBookings);
        [$revenueDesc, $revenueIcon, $revenueColor] = $this->buildDescriptionWithDelta('Bookings starting in period', $revenue, $prevRevenue);

        return [
            Stat::make('Capacity-Nights Booked', number_format($bookedCapacityNights))
                ->description($bookedDesc)
                ->descriptionIcon($bookedIcon)
                ->descriptionColor($bookedColor)
                ->color('primary'),
            Stat::make('Occupancy Rate', number_format($occupancyRate, 1) . '%')
                ->description($occupancyDesc)
                ->descriptionIcon($occupancyIcon)
                ->descriptionColor($occupancyColor)
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

    private function calculateBookedCapacityNights(Carbon $start, Carbon $end): int
    {
        $bookings = HotelBooking::query()
            ->where('status', '!=', 'canceled')
            ->where('start_date', '<', $end)
            ->where('end_date', '>', $start)
            ->whereHas('room.hotel', fn ($query) => $query->where('user_id', auth()->id()))
            ->get(['start_date', 'end_date', 'quantity']);

        $total = 0;
        foreach ($bookings as $booking) {
            $bookingStart = Carbon::parse($booking->start_date);
            $bookingEnd = Carbon::parse($booking->end_date);

            $overlapStart = $bookingStart->greaterThan($start) ? $bookingStart : $start;
            $overlapEnd = $bookingEnd->lessThan($end) ? $bookingEnd : $end;

            $nights = max(0, $overlapStart->diffInDays($overlapEnd));
            $total += $nights * (int) $booking->quantity;
        }

        return $total;
    }

    private function calculateOccupancyRate(int $bookedCapacityNights, Carbon $start, Carbon $end): float
    {
        $totalCapacity = (int) Room::query()
            ->whereHas('hotel', fn ($query) => $query->where('user_id', auth()->id()))
            ->sum('max_occupancy');

        $days = max(1, $start->diffInDays($end));
        $availableCapacityNights = $totalCapacity * $days;

        if ($availableCapacityNights <= 0) {
            return 0.0;
        }

        return ($bookedCapacityNights / $availableCapacityNights) * 100;
    }

    private function countPendingBookings(Carbon $start, Carbon $end): int
    {
        return HotelBooking::query()
            ->where('status', 'pending')
            ->where('start_date', '>=', $start)
            ->where('start_date', '<', $end)
            ->whereHas('room.hotel', fn ($query) => $query->where('user_id', auth()->id()))
            ->count();
    }

    private function sumRevenue(Carbon $start, Carbon $end): float
    {
        return (float) HotelBooking::query()
            ->where('status', '!=', 'canceled')
            ->where('start_date', '>=', $start)
            ->where('start_date', '<', $end)
            ->whereHas('room.hotel', fn ($query) => $query->where('user_id', auth()->id()))
            ->sum('total_price');
    }
}
