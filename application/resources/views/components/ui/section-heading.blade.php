@props([
    'title',
    'subtitle' => null,
    'align' => 'left',
    'size' => 'lg',
])

@php
    $titleSize = [
        'sm' => 'text-xl',
        'md' => 'text-2xl',
        'lg' => 'text-3xl',
        'xl' => 'text-4xl',
    ][$size] ?? 'text-3xl';

    $alignClass = [
        'left' => 'text-left',
        'center' => 'text-center',
        'right' => 'text-right',
    ][$align] ?? 'text-left';
@endphp

<div {{ $attributes->class([$alignClass]) }}>
    <h2 class="{{ $titleSize }} font-bold horror-font">{{ $title }}</h2>
    @if ($subtitle)
        <p class="mt-2 text-gray-300">{{ $subtitle }}</p>
    @endif
</div>
