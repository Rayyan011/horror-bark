<div id="{{ $mapId }}" style="height: 100vh;"></div>
<link href="https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.css" rel="stylesheet" />
<script src="https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.js"></script>
<script>
    mapboxgl.accessToken = '{{ config('mapsuite.mapbox.access_token') }}';
    const map = new mapboxgl.Map({
        container: '{{ $mapId }}',
        style: 'mapbox://styles/{{ $style }}',
        center: {{ json_encode($centerPoint) }},
        zoom: {{ $zoomLevel }}
    });

    @foreach ($markers as $m)
        new mapboxgl.Marker()
            .setLngLat([{{ $m['lng'] ?? $m[1] }}, {{ $m['lat'] ?? $m[0] }}])
        @if (!empty($m['info']))
            .setPopup(new mapboxgl.Popup().setHTML(`{!! $m['info'] !!}`))
        @endif
        .addTo(map);
    @endforeach
</script>
