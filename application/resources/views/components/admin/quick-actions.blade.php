@props([
    'actions' => [],
    'emptyState' => null,
])

<x-ui.surface>
    @if (empty($actions) && $emptyState)
        <p class="text-sm text-gray-400">{{ $emptyState }}</p>
    @endif

    <div class="flex flex-wrap gap-2">
        @foreach ($actions as $action)
            <x-ui.button :href="$action['url']" :variant="$action['variant'] ?? 'primary'">
                {{ $action['label'] }}
            </x-ui.button>
        @endforeach
    </div>
</x-ui.surface>
