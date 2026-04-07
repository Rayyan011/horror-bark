@props([
    'hotel',
    'cheapestRoom' => null,
    'viewUrl' => null,
])

@php
    $resolvedCheapestRoom = $cheapestRoom ?: $hotel->rooms->sortBy('price')->first();
    $district = filled($hotel->location ?? null)
        ? trim(\Illuminate\Support\Str::before($hotel->location, '·'))
        : null;
@endphp

<x-ui.entity-card
    :title="$hotel->name"
    :media="[
        'images' => $hotel->images ?? [],
        'fallback' => \App\Support\HorrorGeneratedMediaCatalog::path('fallbacks', 'hotel'),
        'alt' => $hotel->name,
    ]"
    :meta="[
        ['label' => 'Location', 'value' => $hotel->location ?? 'N/A', 'tone' => 'muted'],
        ['label' => 'District', 'value' => $district ?: 'Horror-Bark', 'tone' => 'muted'],
        ['label' => 'From', 'value' => $resolvedCheapestRoom ? 'MVR ' . number_format($resolvedCheapestRoom->price, 2) . ' / night' : 'N/A'],
    ]"
    :description="$hotel->description"
    :actions="[
        ['label' => 'View Hotel', 'href' => $viewUrl ?: route('hotels.show', $hotel), 'variant' => 'primary'],
    ]"
/>
