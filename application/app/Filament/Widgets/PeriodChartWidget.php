<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

abstract class PeriodChartWidget extends ChartWidget
{
    public ?string $filter = '7d';

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            '7d' => 'Last 7 days',
            '30d' => 'Last 30 days',
            'this_month' => 'This month',
        ];
    }

    public function updatedFilter(): void
    {
        $this->cachedData = null;
    }

    /**
     * @return array{0: Carbon, 1: Carbon, 2: int}
     */
    protected function getFilterRange(): array
    {
        $now = Carbon::now();

        return match ($this->filter) {
            'today' => [$now->copy()->startOfDay(), $now->copy()->addDay()->startOfDay(), 1],
            '30d' => [$now->copy()->subDays(29)->startOfDay(), $now->copy()->addDay()->startOfDay(), 30],
            'this_month' => [
                $now->copy()->startOfMonth(),
                $now->copy()->addMonth()->startOfMonth(),
                $now->copy()->daysInMonth,
            ],
            default => [$now->copy()->subDays(6)->startOfDay(), $now->copy()->addDay()->startOfDay(), 7],
        };
    }
}
