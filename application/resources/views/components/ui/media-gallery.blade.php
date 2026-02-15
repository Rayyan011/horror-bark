@props([
    'images' => [],
    'fallbackSrc' => null,
    'alt' => 'Image',
    'mode' => 'auto',
    'height' => 'h-48',
])

@php
    $resolvedImages = collect($images)
        ->filter(fn ($image) => filled($image))
        ->map(function ($image) {
            if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://') || str_starts_with($image, '/')) {
                return $image;
            }

            return asset('storage/' . ltrim($image, '/'));
        })
        ->values();

    $totalSlides = $resolvedImages->count();
    $resolvedFallback = $fallbackSrc;

    if ($resolvedFallback && !str_starts_with($resolvedFallback, 'http://') && !str_starts_with($resolvedFallback, 'https://') && !str_starts_with($resolvedFallback, '/')) {
        $resolvedFallback = asset($resolvedFallback);
    }

    $shouldCarousel = $mode === 'carousel' || ($mode === 'auto' && $totalSlides > 1);
@endphp

@if ($totalSlides === 0)
    @if ($resolvedFallback)
        <img src="{{ $resolvedFallback }}" alt="{{ $alt }}" class="w-full {{ $height }} object-cover" loading="lazy" />
    @endif
@elseif ($shouldCarousel)
    <div
        x-data="{ currentSlide: 0, totalSlides: {{ $totalSlides }}, interval: null }"
        x-init="
            interval = setInterval(() => {
                currentSlide = (currentSlide + 1) % totalSlides;
            }, 3000);
        "
        x-on:mouseleave="
            if (!interval) {
                interval = setInterval(() => {
                    currentSlide = (currentSlide + 1) % totalSlides;
                }, 3000);
            }
        "
        x-on:mouseenter="
            if (interval) {
                clearInterval(interval);
                interval = null;
            }
        "
        class="relative {{ $height }} overflow-hidden"
    >
        @foreach ($resolvedImages as $index => $image)
            <div
                x-show="currentSlide === {{ $index }}"
                x-transition:enter="transition ease-out duration-700"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-700 absolute inset-0"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="w-full h-full"
            >
                <img src="{{ $image }}" alt="{{ $alt }} - Image {{ $index + 1 }}" class="w-full {{ $height }} object-cover" loading="lazy" />
            </div>
        @endforeach

        <div class="absolute bottom-2 left-1/2 transform -translate-x-1/2 flex space-x-2" x-show="totalSlides > 1">
            @foreach ($resolvedImages as $index => $image)
                <button
                    @click="currentSlide = {{ $index }}; clearInterval(interval); interval = null;"
                    :class="{ 'bg-white': currentSlide === {{ $index }}, 'bg-gray-400': currentSlide !== {{ $index }} }"
                    class="w-2 h-2 rounded-full hover:bg-white focus:outline-none"
                    aria-label="Go to slide {{ $index + 1 }}"
                ></button>
            @endforeach
        </div>
    </div>
@else
    <img src="{{ $resolvedImages->first() }}" alt="{{ $alt }}" class="w-full {{ $height }} object-cover" loading="lazy" />
@endif
