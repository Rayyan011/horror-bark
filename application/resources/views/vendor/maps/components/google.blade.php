<div id="{{ $mapId }}" style="height: 100vh;"></div>
<script async
    src="https://maps.googleapis.com/maps/api/js?key={{ config('mapsuite.google.api_key') }}&callback=initMap"></script>
<script>
    function initMap() {
        const map = new google.maps.Map(document.getElementById('{{ $mapId }}'), {
            zoom: {{ $zoomLevel }},
            center: {
                lat: {{ $centerPoint[0] }},
                lng: {{ $centerPoint[1] }}
            },
            mapTypeId: '{{ $mapType }}'
        });

        const bounds = new google.maps.LatLngBounds();

        @foreach ($markers as $m)
            const pos = {
                lat: {{ $m['lat'] ?? $m[0] }},
                lng: {{ $m['lng'] ?? $m[1] }}
            };
            const marker = new google.maps.Marker({
                position: pos,
                map,
                title: '{{ $m['label'] ?? '' }}'
            });

            @if (!empty($m['info']))
                const infowindow = new google.maps.InfoWindow({
                    content: `{!! $m['info'] !!}`
                });
                marker.addListener('click', () => infowindow.open(map, marker));
                @if (!empty($m['open']))
                    infowindow.open(map, marker);
                @endif
            @endif

            bounds.extend(pos);
        @endforeach

        @if ($fitToBounds)
            map.fitBounds(bounds);
            @if ($centerToBoundsCenter)
                map.setCenter(bounds.getCenter());
            @endif
        @endif
    }
</script>
