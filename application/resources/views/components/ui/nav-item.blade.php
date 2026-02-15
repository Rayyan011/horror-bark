@props([
    'label',
    'href' => '#',
    'active' => false,
    'icon' => null,
])

<a
    href="{{ $href }}"
    {{ $attributes->class([
        'inline-flex items-center gap-2 transition-colors',
        'text-white font-semibold' => $active,
        'text-gray-200 hover:text-gray-400' => !$active,
    ]) }}
>
    @if ($icon)
        <span aria-hidden="true">{{ $icon }}</span>
    @endif
    <span>{{ $label }}</span>
</a>
