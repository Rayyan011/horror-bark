@props([
    'center' => [4.22700104517645, 73.42662978621766],
    'zoom' => 16,
    'markers' => [],
    'height' => 'h-96',
    'interactive' => false,
])

<div class="relative overflow-hidden rounded-sm border border-primary-light/30 bg-background-dark shadow-[inset_0_0_50px_rgba(0,0,0,1)] {{ $height }}">
    <div class="h-full w-full horror-map-shell">
        <x-maps-leaflet
            :center-point="$center"
            :zoom-level="$zoom"
            :zoom-control="$interactive"
            :scroll-wheel-zoom="$interactive"
            :markers="$markers"
        />
    </div>
</div>
