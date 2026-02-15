@props([
    'variant' => 'default',
    'label' => null,
])

@php
    $classes = match ($variant) {
        'success' => 'bg-green-700 text-white',
        'danger' => 'bg-red-700 text-white',
        'warning' => 'bg-yellow-700 text-white',
        'muted' => 'bg-gray-700 text-gray-200',
        default => 'bg-gray-600 text-white',
    };
@endphp

<span {{ $attributes->class([$classes, 'inline-flex items-center px-2 py-1 rounded text-xs uppercase tracking-wide']) }}>
    {{ $label ?? $slot }}
</span>
