<link href="{{ 'https://unpkg.com/leaflet@' . $leafletVersion . '/dist/leaflet.css' }}" rel="stylesheet" crossorigin="" />
@if (!empty($enableClustering))
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
@endif

<style>
    #{{ $mapId }} {
        height: 100%;
        min-height: 10vh;
        width: 100%;
    }
</style>

<div id="{{ $mapId }}"></div>

<script src="{{ 'https://unpkg.com/leaflet@' . $leafletVersion . '/dist/leaflet.js' }}" crossorigin=""></script>
@if (!empty($enableClustering))
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
@endif


<script>
    window.addEventListener('load', function() {

        const map = L.map('{{ $mapId }}').setView({{ json_encode($centerPoint) }}, {{ $zoomLevel }});

        let tileUrl = '{{ $tileHost }}';
        if (tileUrl === 'mapbox') {
            tileUrl =
                'https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={{ config('mapsuite.mapbox.access_token') }}';
        } else if (tileUrl === 'openstreetmap') {
            tileUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
        }

        L.tileLayer(tileUrl, {
            maxZoom: {{ $maxZoomLevel }},
            attribution: `{!! $attribution !!}`,
            id: 'mapbox/streets-v11',
            tileSize: 512,
            zoomOffset: -1
        }).addTo(map);

        const popupOpts = {!! json_encode($popupOptions ?? []) !!};

        @if (!empty($enableClustering))
            const markerCluster = L.markerClusterGroup();
        @endif

        @foreach ($markers as $index => $m)
            let marker{{ $index }} = L.marker([{{ $m['lat'] ?? $m[0] }}, {{ $m['lng'] ?? $m[1] }}], {
                @if (!empty($m['icon']))
                    icon: L.icon({
                        iconUrl: '{{ asset($m['icon']) }}',
                        iconSize: [{{ $m['iconSizeX'] ?? 32 }}, {{ $m['iconSizeY'] ?? 32 }}]
                    }),
                @endif
                @if (!empty($m['draggable']))
                    draggable: true,
                @endif
                @if (!empty($m['label']))
                    title: '{{ $m['label'] }}',
                @endif
            });

            @if (!empty($m['info']))
                marker{{ $index }}.bindPopup(`{!! $m['info'] !!}`, popupOpts);
            @endif

            @if (!empty($enableClustering))
                markerCluster.addLayer(marker{{ $index }});
            @else
                marker{{ $index }}.addTo(map);
            @endif

            @if (!empty($m['open']) && empty($enableClustering))
                marker{{ $index }}.openPopup();
            @endif
        @endforeach


        @if (!empty($enableClustering))
            map.addLayer(markerCluster);
        @endif
    });
</script>
