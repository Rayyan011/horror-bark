@props([
    'submitLabel' => 'Submit',
    'resetHref' => null,
    'secondary' => [],
    'submitVariant' => 'primary',
])

<div {{ $attributes->class(['flex flex-wrap gap-2']) }}>
    <x-ui.button type="submit" :variant="$submitVariant">{{ $submitLabel }}</x-ui.button>

    @if ($resetHref)
        <x-ui.button :href="$resetHref" variant="ghost">Reset</x-ui.button>
    @endif

    @foreach ($secondary as $item)
        <x-ui.button
            :href="$item['href'] ?? '#'"
            :variant="$item['variant'] ?? 'secondary'"
            :method="$item['method'] ?? 'GET'"
        >
            {{ $item['label'] ?? 'Action' }}
        </x-ui.button>
    @endforeach
</div>
