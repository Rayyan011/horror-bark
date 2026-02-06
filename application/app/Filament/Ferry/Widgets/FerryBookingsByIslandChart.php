<?php

namespace App\Filament\Ferry\Widgets;

use App\Filament\Widgets\PeriodChartWidget;
use App\Models\FerryBooking;
use Illuminate\Support\Facades\DB;

class FerryBookingsByIslandChart extends PeriodChartWidget
{
    protected static ?string $heading = 'Bookings by Island';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $ownerId = auth()->id();
        [$start, $end] = $this->getFilterRange();

        $rows = FerryBooking::query()
            ->join('ferries', 'ferry_bookings.ferry_id', '=', 'ferries.id')
            ->join('islands', 'ferries.island_id', '=', 'islands.id')
            ->where('ferries.user_id', $ownerId)
            ->where('ferry_bookings.status', '!=', 'canceled')
            ->where('ferry_bookings.booking_time', '>=', $start)
            ->where('ferry_bookings.booking_time', '<', $end)
            ->groupBy('islands.name')
            ->orderByDesc(DB::raw('SUM(ferry_bookings.quantity)'))
            ->selectRaw('islands.name as island, SUM(ferry_bookings.quantity) as qty')
            ->get();

        $labels = $rows->pluck('island')->toArray();
        $data = $rows->pluck('qty')->map(fn ($value) => (int) $value)->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Bookings',
                    'data' => $data,
                    'backgroundColor' => [
                        '#f59e0b',
                        '#3b82f6',
                        '#22c55e',
                        '#ef4444',
                        '#8b5cf6',
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
