@props([
    'variant' => 'primary',
    'size' => 'md',
    'block' => false,
    'href' => null,
    'type' => 'button',
    'loading' => false,
    'method' => 'GET',
])

@php
    $variantClass = match ($variant) {
        'secondary' => 'border border-primary-light/35 bg-transparent text-primary-light hover:border-primary-light hover:bg-primary-dark/70 hover:text-white',
        'danger' => 'border border-rose-700/60 bg-rose-900/70 text-rose-100 hover:bg-rose-800',
        'ghost' => 'border border-primary-light/30 bg-background-dark/60 text-primary-light hover:border-primary-light hover:bg-white/5 hover:text-white',
        default => 'border border-primary-light/30 bg-primary-dark text-moonlight hover:border-primary-light hover:bg-primary',
    };

    $sizeClass = match ($size) {
        'sm' => 'px-3 py-1.5 text-xs uppercase tracking-[0.15em]',
        'lg' => 'px-6 py-3 text-base uppercase tracking-[0.18em]',
        default => 'px-4 py-2 text-sm uppercase tracking-[0.16em]',
    };

    $classes = trim('inline-flex items-center justify-center gap-2 font-display transition duration-300 ' . $variantClass . ' ' . $sizeClass . ($block ? ' w-full text-center' : ''));
@endphp

@if ($href && strtoupper($method) === 'GET')
    <a href="{{ $href }}" {{ $attributes->class([$classes]) }}>
        @if ($loading)
            <span class="opacity-70">Loading...</span>
        @else
            {{ $slot }}
        @endif
    </a>
@elseif ($href && strtoupper($method) !== 'GET')
    <form method="POST" action="{{ $href }}" class="{{ $block ? 'w-full' : '' }}">
        @csrf
        @if (!in_array(strtoupper($method), ['GET', 'POST'], true))
            @method($method)
        @endif
        <button type="submit" class="{{ $classes }} {{ $block ? 'w-full' : '' }}">
            @if ($loading)
                <span class="opacity-70">Loading...</span>
            @else
                {{ $slot }}
            @endif
        </button>
    </form>
@else
    <button type="{{ $type }}" {{ $attributes->class([$classes]) }}>
        @if ($loading)
            <span class="opacity-70">Loading...</span>
        @else
            {{ $slot }}
        @endif
    </button>
@endif
