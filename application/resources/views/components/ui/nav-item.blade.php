@props([
    'label',
    'href' => '#',
    'active' => false,
    'icon' => null,
])

<a
    href="{{ $href }}"
    {{ $attributes->class([
        'inline-flex items-center gap-2 border-b border-transparent pb-1 font-serif text-xs uppercase tracking-[0.2em] transition-all duration-300',
        'border-primary-light/70 text-moonlight' => $active,
        'text-primary-light hover:border-primary-light/60 hover:text-white' => !$active,
    ]) }}
>
    @if ($icon)
        <span aria-hidden="true">{{ $icon }}</span>
    @endif
    <span>{{ $label }}</span>
</a>
