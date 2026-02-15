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
        'secondary' => 'bg-gray-700 text-white hover:bg-gray-600',
        'danger' => 'bg-red-700 text-white hover:bg-red-600',
        'ghost' => 'border border-gray-600 text-gray-200 hover:text-white',
        default => 'bg-red-600 text-white hover:bg-red-700',
    };

    $sizeClass = match ($size) {
        'sm' => 'px-3 py-1 text-sm',
        'lg' => 'px-5 py-3 text-lg',
        default => 'px-4 py-2',
    };

    $classes = trim($variantClass . ' ' . $sizeClass . ' rounded transition ' . ($block ? 'w-full text-center' : ''));
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
