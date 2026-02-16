@props([
    'variant' => 'default',
    'padding' => 'p-6',
    'border' => true,
    'shadow' => true,
    'interactive' => false,
])

@php
    $base = match ($variant) {
        'muted' => 'bg-background-dark/85',
        'highlight' => 'bg-primary/80',
        'danger' => 'bg-rose-900/70',
        default => 'bg-primary-dark/90',
    };
@endphp

<div
    {{ $attributes->class([
        $base,
        $padding,
        'rounded-sm',
        'border border-primary-light/20' => $border,
        'shadow-cold-shadow' => $shadow,
        'transition duration-300 hover:-translate-y-0.5 hover:border-primary-light/45' => $interactive,
    ]) }}
>
    {{ $slot }}
</div>
