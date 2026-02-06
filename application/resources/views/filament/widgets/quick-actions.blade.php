@php
    $actions = $this->getActions();
    $heading = method_exists($this, 'getHeading') ? $this->getHeading() : 'Quick actions';
    $emptyStateMessage = method_exists($this, 'getEmptyStateMessage') ? $this->getEmptyStateMessage() : null;
    $showEmptyState = method_exists($this, 'shouldShowEmptyState') ? $this->shouldShowEmptyState() : false;
@endphp

<x-filament-widgets::widget>
    <x-filament::section :heading="$heading">
        @if ($showEmptyState && filled($emptyStateMessage))
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ $emptyStateMessage }}
            </p>
        @endif

        <div class="flex flex-wrap gap-2">
            @foreach ($actions as $action)
                <x-filament::button
                    tag="a"
                    :href="$action['url']"
                    :icon="$action['icon'] ?? null"
                    :color="$action['color'] ?? 'primary'"
                >
                    {{ $action['label'] }}
                </x-filament::button>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
