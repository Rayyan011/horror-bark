<?php

namespace App\Filament\Ferry\Widgets;

use App\Filament\Widgets\PeriodChartWidget;
use App\Models\Ferry;
use App\Models\FerryBooking;

class FerryLoadFactorByHourChart extends PeriodChartWidget
{
    protected static ?string $heading = 'Load Factor by Hour';

    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $ownerId = auth()->id();
        [$start, $end] = $this->getFilterRange();
        $days = max(1, $start->diffInDays($end));

        $totalCapacity = (int) Ferry::query()
            ->where('user_id', $ownerId)
            ->sum('max_capacity');

        $capacityPerHour = $totalCapacity * $days;

        $bookedByHour = FerryBooking::query()
            ->whereHas('ferry', fn ($query) => $query->where('user_id', $ownerId))
            ->where('status', '!=', 'canceled')
            ->where('booking_time', '>=', $start)
            ->where('booking_time', '<', $end)
            ->selectRaw('HOUR(booking_time) as hour, SUM(quantity) as qty')
            ->groupBy('hour')
            ->pluck('qty', 'hour');

        $hours = range(9, 16);
        $labels = array_map(fn ($hour) => sprintf('%02d:00', $hour), $hours);
        $data = [];

        foreach ($hours as $hour) {
            $qty = (int) ($bookedByHour[$hour] ?? 0);
            $data[] = $capacityPerHour > 0 ? round(($qty / $capacityPerHour) * 100, 1) : 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Load %',
                    'data' => $data,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => '#3b82f6',
                    'tension' => 0.3,
                    'fill' => false,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
