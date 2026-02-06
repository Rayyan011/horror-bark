<?php

namespace App\Filament\Ride\Widgets;

use App\Filament\Widgets\PeriodChartWidget;
use App\Models\Ride;
use App\Models\RideBooking;

class RideCapacityUtilizationBySlotChart extends PeriodChartWidget
{
    protected static ?string $heading = 'Capacity Utilization by Slot';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $ownerId = auth()->id();
        [$start, $end] = $this->getFilterRange();
        $days = max(1, $start->diffInDays($end));

        $totalCapacity = (int) Ride::query()
            ->where('user_id', $ownerId)
            ->sum('max_capacity');

        $capacityPerSlot = $totalCapacity * $days;

        $bookedByHour = RideBooking::query()
            ->whereHas('ride', fn ($query) => $query->where('user_id', $ownerId))
            ->where('status', '!=', 'canceled')
            ->where('booking_time', '>=', $start)
            ->where('booking_time', '<', $end)
            ->selectRaw('HOUR(booking_time) as hour, SUM(quantity) as qty')
            ->groupBy('hour')
            ->pluck('qty', 'hour');

        $slots = [9, 17];
        $labels = ['09:00', '17:00'];
        $data = [];

        foreach ($slots as $hour) {
            $qty = (int) ($bookedByHour[$hour] ?? 0);
            $data[] = $capacityPerSlot > 0 ? round(($qty / $capacityPerSlot) * 100, 1) : 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Utilization %',
                    'data' => $data,
                    'backgroundColor' => ['#f59e0b', '#3b82f6'],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
