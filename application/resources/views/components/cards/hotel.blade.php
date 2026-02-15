@props([
    'hotel',
    'cheapestRoom' => null,
    'viewUrl' => null,
])

@php
    $resolvedCheapestRoom = $cheapestRoom ?: $hotel->rooms->sortBy('price')->first();
@endphp

<x-ui.entity-card
    :title="$hotel->name"
    :media="[
        'images' => $hotel->images ?? [],
        'fallback' => 'https://picsum.photos/seed/' . $hotel->id . '/400/300',
        'alt' => $hotel->name,
    ]"
    :meta="[
        ['label' => 'Location', 'value' => $hotel->location ?? 'N/A', 'tone' => 'muted'],
        ['label' => 'Island', 'value' => 'Horror Island', 'tone' => 'muted'],
        ['label' => 'From', 'value' => $resolvedCheapestRoom ? 'MVR ' . number_format($resolvedCheapestRoom->price, 2) . ' / night' : 'N/A'],
    ]"
    :actions="[
        ['label' => 'View Hotel', 'href' => $viewUrl ?: route('hotels.show', $hotel), 'variant' => 'primary'],
    ]"
/>
