<?php

namespace App\Filament\Widgets;

use App\Models\BeachEventBooking;
use App\Models\FerryBooking;
use App\Models\GameBooking;
use App\Models\HotelBooking;
use App\Models\RideBooking;
use App\Filament\Widgets\PeriodChartWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class BookingsByDayChart extends PeriodChartWidget
{
    protected static ?string $heading = 'Bookings by Day';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        [$startDate, $endDate, $days] = $this->getFilterRange();
        $labels = [];
        $dateKeys = [];

        for ($i = 0; $i < $days; $i++) {
            $date = $startDate->copy()->addDays($i)->startOfDay();
            $labels[] = $date->format('M j');
            $dateKeys[] = $date->format('Y-m-d');
        }

        return [
            'datasets' => [
                $this->makeDataset('Hotels', HotelBooking::query()->where('status', '!=', 'canceled'), $startDate, $endDate, $dateKeys, '#f59e0b', 'start_date'),
                $this->makeDataset('Ferries', FerryBooking::query()->where('status', '!=', 'canceled'), $startDate, $endDate, $dateKeys, '#3b82f6', 'booking_time'),
                $this->makeDataset('Rides', RideBooking::query()->where('status', '!=', 'canceled'), $startDate, $endDate, $dateKeys, '#22c55e', 'booking_time'),
                $this->makeDataset('Games', GameBooking::query()->where('status', '!=', 'canceled'), $startDate, $endDate, $dateKeys, '#8b5cf6', 'booking_time'),
                $this->makeDataset('Beach Events', BeachEventBooking::query()->where('status', '!=', 'canceled'), $startDate, $endDate, $dateKeys, '#ef4444', 'booking_date'),
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    private function makeDataset(
        string $label,
        Builder $query,
        Carbon $startDate,
        Carbon $endDate,
        array $dateKeys,
        string $color,
        string $dateColumn
    ): array {
        $counts = $query
            ->where($dateColumn, '>=', $startDate)
            ->where($dateColumn, '<', $endDate)
            ->selectRaw(sprintf('DATE(%s) as date, COUNT(*) as count', $dateColumn))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');

        $data = [];
        foreach ($dateKeys as $dateKey) {
            $data[] = (int) ($counts[$dateKey] ?? 0);
        }

        return [
            'label' => $label,
            'data' => $data,
            'borderColor' => $color,
            'backgroundColor' => $color,
            'tension' => 0.3,
            'fill' => false,
        ];
    }
}
