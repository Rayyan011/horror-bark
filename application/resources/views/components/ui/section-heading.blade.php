@props([
    'title',
    'subtitle' => null,
    'align' => 'left',
    'size' => 'lg',
])

@php
    $titleSize = [
        'sm' => 'text-xl md:text-2xl',
        'md' => 'text-3xl md:text-4xl',
        'lg' => 'text-4xl md:text-5xl',
        'xl' => 'text-4xl md:text-6xl',
    ][$size] ?? 'text-4xl md:text-5xl';

    $alignClass = [
        'left' => 'text-left',
        'center' => 'text-center',
        'right' => 'text-right',
    ][$align] ?? 'text-left';
@endphp

<div {{ $attributes->class([$alignClass]) }}>
    <h2 class="{{ $titleSize }} gothic-title drop-shadow-md">{{ $title }}</h2>
    @if ($subtitle)
        <p class="gothic-subtitle mt-3 text-base md:text-lg">{{ $subtitle }}</p>
    @endif
</div>
