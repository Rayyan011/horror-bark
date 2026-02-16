@props([
    'title',
    'media' => null,
    'meta' => [],
    'description' => null,
    'actions' => [],
    'theme' => 'default',
])

<x-ui.surface :variant="$theme" padding="p-0" class="overflow-hidden flex flex-col h-full" interactive>
    @if ($media)
        <x-ui.media-gallery
            :images="$media['images'] ?? []"
            :fallback-src="$media['fallback'] ?? null"
            :alt="$media['alt'] ?? $title"
            :mode="$media['mode'] ?? 'auto'"
        />
    @endif

    <div class="flex flex-1 flex-col p-5">
        <div class="space-y-2">
            <h4 class="font-display text-2xl tracking-wide text-moonlight">{{ $title }}</h4>

            @foreach ($meta as $item)
                <p class="text-sm font-serif {{ ($item['tone'] ?? 'default') === 'muted' ? 'text-primary-light/80' : 'text-primary-light' }}">
                    <span class="font-semibold text-moonlight">{{ $item['label'] }}:</span> {{ $item['value'] }}
                </p>
            @endforeach

            @if ($description)
                <p class="font-serif text-sm leading-relaxed text-primary-light">{{ $description }}</p>
            @endif

            @isset($details)
                {{ $details }}
            @endisset
        </div>

        @if (!empty($actions))
            <div class="mt-4 flex flex-wrap gap-2">
                @foreach ($actions as $action)
                    <x-ui.button
                        :href="$action['href'] ?? null"
                        :method="$action['method'] ?? 'GET'"
                        :variant="$action['variant'] ?? 'primary'"
                        :block="$action['block'] ?? false"
                        :size="$action['size'] ?? 'md'"
                    >
                        {{ $action['label'] }}
                    </x-ui.button>
                @endforeach
            </div>
        @endif

        @isset($footer)
            <div class="mt-4">
                {{ $footer }}
            </div>
        @endisset
    </div>
</x-ui.surface>
