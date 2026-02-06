<?php

namespace App\Filament\Ferry\Widgets;

use App\Models\FerryBooking;
use App\Filament\Widgets\PeriodChartWidget;
use Illuminate\Support\Carbon;

class FerryBookingsByDayChart extends PeriodChartWidget
{
    protected static ?string $heading = 'Ferry Bookings by Day';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $ownerId = auth()->id();
        [$startDate, $endDate, $days] = $this->getFilterRange();
        $labels = [];
        $dateKeys = [];

        for ($i = 0; $i < $days; $i++) {
            $date = $startDate->copy()->addDays($i)->startOfDay();
            $labels[] = $date->format('M j');
            $dateKeys[] = $date->format('Y-m-d');
        }

        $counts = FerryBooking::query()
            ->whereHas('ferry', fn ($query) => $query->where('user_id', $ownerId))
            ->where('status', '!=', 'canceled')
            ->where('booking_time', '>=', $startDate)
            ->where('booking_time', '<', $endDate)
            ->selectRaw('DATE(booking_time) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');

        $data = [];
        foreach ($dateKeys as $dateKey) {
            $data[] = (int) ($counts[$dateKey] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Bookings',
                    'data' => $data,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => '#f59e0b',
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
