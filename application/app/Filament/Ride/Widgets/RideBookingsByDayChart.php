<?php

namespace App\Filament\Ride\Widgets;

use App\Models\RideBooking;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RideBookingsByDayChart extends ChartWidget
{
    protected static ?string $heading = 'Ride Bookings by Day (Last 14 Days)';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $ownerId = auth()->id();
        $days = 14;
        $labels = [];
        $dateKeys = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->startOfDay();
            $labels[] = $date->format('M j');
            $dateKeys[] = $date->format('Y-m-d');
        }

        $startDate = Carbon::now()->subDays($days - 1)->startOfDay();

        $counts = RideBooking::query()
            ->whereHas('ride', fn ($query) => $query->where('user_id', $ownerId))
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
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
