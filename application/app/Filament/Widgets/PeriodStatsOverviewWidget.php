<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasDashboardDateRange;
use Filament\Widgets\StatsOverviewWidget;
use Illuminate\Support\Carbon;

abstract class PeriodStatsOverviewWidget extends StatsOverviewWidget
{
    use HasDashboardDateRange;

    protected static string $view = 'filament.widgets.stats-overview-with-filters';

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function getPeriodRange(): array
    {
        return $this->getDashboardDateRange();
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function getPreviousPeriodRange(): array
    {
        return $this->getPreviousDashboardDateRange();
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
