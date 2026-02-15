@props([
    'variant' => 'default',
    'padding' => 'p-6',
    'border' => true,
    'shadow' => true,
    'interactive' => false,
])

@php
    $base = match ($variant) {
        'muted' => 'bg-gray-900',
        'highlight' => 'bg-gray-700',
        'danger' => 'bg-red-900',
        default => 'bg-gray-800',
    };
@endphp

<div
    {{ $attributes->class([
        $base,
        $padding,
        'rounded',
        'border border-gray-700' => $border,
        'shadow' => $shadow,
        'transition hover:-translate-y-0.5' => $interactive,
    ]) }}
>
    {{ $slot }}
</div>
