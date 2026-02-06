<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Illuminate\Support\Carbon;

abstract class PeriodStatsOverviewWidget extends StatsOverviewWidget
{
    public string $period = 'this_month';

    protected static string $view = 'filament.widgets.stats-overview-with-filters';

    protected function getPeriodFilters(): array
    {
        return [
            'today' => 'Today',
            '7d' => 'Last 7 days',
            '30d' => 'Last 30 days',
            'this_month' => 'This month',
        ];
    }

    public function updatedPeriod(): void
    {
        $this->cachedStats = null;
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function getPeriodRange(): array
    {
        $now = Carbon::now();

        return match ($this->period) {
            'today' => [$now->copy()->startOfDay(), $now->copy()->addDay()->startOfDay()],
            '7d' => [$now->copy()->subDays(6)->startOfDay(), $now->copy()->addDay()->startOfDay()],
            '30d' => [$now->copy()->subDays(29)->startOfDay(), $now->copy()->addDay()->startOfDay()],
            default => [$now->copy()->startOfMonth(), $now->copy()->addMonth()->startOfMonth()],
        };
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function getPreviousPeriodRange(): array
    {
        [$start, $end] = $this->getPeriodRange();

        if ($this->period === 'this_month') {
            $prevEnd = $start->copy();
            $prevStart = $start->copy()->subMonth()->startOfMonth();

            return [$prevStart, $prevEnd];
        }

        $seconds = $end->diffInSeconds($start);
        $prevEnd = $start->copy();
        $prevStart = $start->copy()->subSeconds($seconds);

        return [$prevStart, $prevEnd];
    }

    /**
     * @return array{0: string, 1: string, 2: string}
     */
    protected function getDeltaDescriptor(float|int $current, float|int $previous): array
    {
        if ($previous == 0) {
            if ($current == 0) {
                return ['0% vs prev period', 'heroicon-m-minus', 'gray'];
            }

            return ['New vs prev period', 'heroicon-m-arrow-trending-up', 'success'];
        }

        $change = (($current - $previous) / $previous) * 100;
        $sign = $change >= 0 ? '+' : '';
        $text = sprintf('%s%.1f%% vs prev period', $sign, $change);
        $icon = $change >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down';
        $color = $change >= 0 ? 'success' : 'danger';

        return [$text, $icon, $color];
    }

    /**
     * @return array{0: string, 1: string, 2: string}
     */
    protected function buildDescriptionWithDelta(string $base, float|int $current, float|int $previous): array
    {
        [$deltaText, $icon, $color] = $this->getDeltaDescriptor($current, $previous);
        $description = $base !== '' ? $base . ' · ' . $deltaText : $deltaText;

        return [$description, $icon, $color];
    }
}
