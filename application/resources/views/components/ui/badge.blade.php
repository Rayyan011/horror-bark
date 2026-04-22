@props([
    'variant' => 'default',
    'label' => null,
])

@php
    $classes = match ($variant) {
        'success' => 'border border-emerald-400/30 bg-emerald-950/70 text-emerald-50',
        'danger' => 'border border-rose-400/30 bg-rose-950/75 text-rose-50',
        'warning' => 'border border-amber-400/30 bg-amber-950/70 text-amber-50',
        'muted' => 'border border-primary-light/15 bg-primary-dark/85 text-primary-light/85',
        default => 'border border-primary-light/20 bg-primary-dark text-moonlight',
    };
@endphp

<span {{ $attributes->class([$classes, 'inline-flex items-center rounded px-2 py-1 font-display text-xs uppercase tracking-[0.16em]']) }}>
    {{ $label ?? $slot }}
</span>
