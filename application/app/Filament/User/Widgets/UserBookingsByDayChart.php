<?php

namespace App\Filament\User\Widgets;

use App\Models\FerryBooking;
use App\Models\GameBooking;
use App\Models\HotelBooking;
use App\Models\RideBooking;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class UserBookingsByDayChart extends ChartWidget
{
    protected static ?string $heading = 'My Bookings by Day (Last 14 Days)';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $userId = auth()->id();
        $days = 14;
        $labels = [];
        $dateKeys = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->startOfDay();
            $labels[] = $date->format('M j');
            $dateKeys[] = $date->format('Y-m-d');
        }

        $startDate = Carbon::now()->subDays($days - 1)->startOfDay();

        return [
            'datasets' => [
                $this->makeDataset('Hotels', HotelBooking::query()->where('user_id', $userId), $startDate, $dateKeys, '#f43f5e'),
                $this->makeDataset('Ferries', FerryBooking::query()->where('user_id', $userId), $startDate, $dateKeys, '#f59e0b'),
                $this->makeDataset('Rides', RideBooking::query()->where('user_id', $userId), $startDate, $dateKeys, '#22c55e'),
                $this->makeDataset('Games', GameBooking::query()->where('user_id', $userId), $startDate, $dateKeys, '#3b82f6'),
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
        $query,
        Carbon $startDate,
        array $dateKeys,
        string $color
    ): array {
        $counts = $query
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
            'label' => $label,
            'data' => $data,
            'borderColor' => $color,
            'backgroundColor' => $color,
            'tension' => 0.3,
            'fill' => false,
        ];
    }
}
