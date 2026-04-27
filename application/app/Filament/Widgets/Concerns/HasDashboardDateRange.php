<?php

namespace App\Filament\Widgets\Concerns;

use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Carbon;

trait HasDashboardDateRange
{
    use InteractsWithPageFilters;

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function getDashboardDateRange(): array
    {
        $now = Carbon::now();

        $start = $this->dashboardFilterDate('startDate', $now->copy()->startOfMonth());
        $end = $this->dashboardFilterDate('endDate', $now);

        if ($end->lt($start)) {
            $end = $start->copy();
        }

        return [$start->startOfDay(), $end->copy()->addDay()->startOfDay()];
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function getPreviousDashboardDateRange(): array
    {
        [$start, $end] = $this->getDashboardDateRange();

        $seconds = max(86400, $end->diffInSeconds($start));
        $previousEnd = $start->copy();

        return [$previousEnd->copy()->subSeconds($seconds), $previousEnd];
    }

    protected function getDashboardRangeDays(): int
    {
        [$start, $end] = $this->getDashboardDateRange();

        return max(1, (int) $start->diffInDays($end));
    }

    protected function getDashboardDateRangeLabel(): string
    {
        [$start, $end] = $this->getDashboardDateRange();
        $inclusiveEnd = $end->copy()->subDay();

        if ($start->isSameDay($inclusiveEnd)) {
            return $start->format('M j, Y');
        }

        return $start->format('M j, Y') . ' - ' . $inclusiveEnd->format('M j, Y');
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function getFirstDashboardDateRange(): array
    {
        [$start] = $this->getDashboardDateRange();

        return [$start, $start->copy()->addDay()];
    }

    protected function getCachedStats(): array
    {
        return $this->getStats();
    }

    protected function getCachedData(): array
    {
        return $this->getData();
    }

    private function dashboardFilterDate(string $key, Carbon $default): Carbon
    {
        $value = $this->filters[$key] ?? null;

        if (blank($value)) {
            return $default->copy()->startOfDay();
        }

        try {
            return Carbon::parse($value)->startOfDay();
        } catch (\Throwable) {
            return $default->copy()->startOfDay();
        }
    }
}
