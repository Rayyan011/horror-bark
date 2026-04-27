<?php

namespace App\Filament\Widgets;

use App\Filament\Widgets\Concerns\HasDashboardDateRange;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

abstract class PeriodChartWidget extends ChartWidget
{
    use HasDashboardDateRange;

    protected int | string | array $columnSpan = 'full';

    /**
     * @return array{0: Carbon, 1: Carbon, 2: int}
     */
    protected function getFilterRange(): array
    {
        [$start, $end] = $this->getDashboardDateRange();

        return [$start, $end, $this->getDashboardRangeDays()];
    }
}
