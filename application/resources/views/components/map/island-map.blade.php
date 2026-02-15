@props([
    'center' => [4.22700104517645, 73.42662978621766],
    'zoom' => 16,
    'markers' => [],
    'height' => 'h-96',
    'interactive' => false,
])

<div class="{{ $height }}">
    <x-maps-leaflet
        :center-point="$center"
        :zoom-level="$zoom"
        :zoom-control="$interactive"
        :scroll-wheel-zoom="$interactive"
        :markers="$markers"
    />
</div>
