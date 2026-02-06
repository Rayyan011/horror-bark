<?php

namespace App\Filament\Hotel\Widgets;

use App\Models\HotelBooking;
use App\Filament\Widgets\PeriodChartWidget;
use Illuminate\Support\Carbon;

class HotelBookingsByDayChart extends PeriodChartWidget
{
    protected static ?string $heading = 'Hotel Bookings by Day';

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

        $counts = HotelBooking::query()
            ->whereHas('room.hotel', fn ($query) => $query->where('user_id', $ownerId))
            ->where('status', '!=', 'canceled')
            ->where('start_date', '>=', $startDate)
            ->where('start_date', '<', $endDate)
            ->selectRaw('DATE(start_date) as date, COUNT(*) as count')
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
                    'borderColor' => '#f43f5e',
                    'backgroundColor' => '#f43f5e',
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
