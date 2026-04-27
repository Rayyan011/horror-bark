@php
    $columns = $this->getColumns();
    $heading = $this->getHeading();
    $description = $this->getDescription();
    $rangeLabel = method_exists($this, 'getDashboardDateRangeLabel') ? $this->getDashboardDateRangeLabel() : null;
    $hasHeading = filled($heading);
    $hasDescription = filled($description);
@endphp

<x-filament-widgets::widget class="fi-wi-stats-overview grid gap-y-4">
    @if ($hasHeading || $hasDescription || $rangeLabel)
        <div class="fi-wi-stats-overview-header flex flex-col gap-y-2 sm:flex-row sm:items-center sm:justify-between">
            <div class="grid gap-y-1">
                @if ($hasHeading)
                    <h3
                        class="fi-wi-stats-overview-header-heading col-span-full text-base font-semibold leading-6 text-gray-950 dark:text-white"
                    >
                        {{ $heading }}
                    </h3>
                @endif

                @if ($hasDescription)
                    <p
                        class="fi-wi-stats-overview-header-description overflow-hidden break-words text-sm text-gray-500 dark:text-gray-400"
                    >
                        {{ $description }}
                    </p>
                @endif
            </div>

            @if ($rangeLabel)
                <div class="w-max rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-600 dark:border-white/10 dark:text-gray-300">
                    Range: {{ $rangeLabel }}
                </div>
            @endif
        </div>
    @endif

    <div
        @if ($pollingInterval = $this->getPollingInterval())
            wire:poll.{{ $pollingInterval }}
        @endif
        @class([
            'fi-wi-stats-overview-stats-ctn grid gap-6',
            'md:grid-cols-1' => $columns === 1,
            'md:grid-cols-2' => $columns === 2,
            'md:grid-cols-3' => $columns === 3,
            'md:grid-cols-2 xl:grid-cols-4' => $columns === 4,
        ])
    >
        @foreach ($this->getCachedStats() as $stat)
            {{ $stat }}
        @endforeach
    </div>
</x-filament-widgets::widget>
