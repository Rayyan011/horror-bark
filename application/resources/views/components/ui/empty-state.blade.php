@props([
    'title' => 'Nothing to show',
    'description' => null,
    'actionLabel' => null,
    'actionHref' => null,
])

<div class="text-center py-10 text-gray-300">
    <h3 class="text-xl font-semibold">{{ $title }}</h3>

    @if ($description)
        <p class="mt-2">{{ $description }}</p>
    @endif

    @if ($actionLabel && $actionHref)
        <div class="mt-4">
            <x-ui.button :href="$actionHref" variant="secondary">{{ $actionLabel }}</x-ui.button>
        </div>
    @endif
</div>
