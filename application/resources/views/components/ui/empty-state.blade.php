@props([
    'title' => 'Nothing to show',
    'description' => null,
    'actionLabel' => null,
    'actionHref' => null,
])

<div class="py-10 text-center">
    <p class="theme-kicker">No Current Match</p>
    <h3 class="mt-2 text-2xl font-semibold text-moonlight">{{ $title }}</h3>

    @if ($description)
        <p class="readable-copy mx-auto mt-3 max-w-2xl">{{ $description }}</p>
    @endif

    @if ($actionLabel && $actionHref)
        <div class="mt-4">
            <x-ui.button :href="$actionHref" variant="secondary">{{ $actionLabel }}</x-ui.button>
        </div>
    @endif
</div>
