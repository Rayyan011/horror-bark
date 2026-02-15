@extends('layouts.app')

@section('title', 'Hotels - Horror-Bark Theme Park')

@section('content')
<main class="space-y-6">
    <x-ui.section-heading title="Our Hotels" size="xl" />

    <x-filters.panel
        :fields="[
            ['label' => 'Search', 'name' => 'search', 'type' => 'text', 'value' => $filters['search'] ?? '', 'placeholder' => 'Hotel or location', 'class' => 'lg:col-span-2'],
            ['label' => 'Min Price', 'name' => 'min_price', 'type' => 'number', 'min' => 0, 'step' => '0.01', 'value' => $filters['min_price'] ?? ''],
            ['label' => 'Max Price', 'name' => 'max_price', 'type' => 'number', 'min' => 0, 'step' => '0.01', 'value' => $filters['max_price'] ?? ''],
            ['label' => 'Min Occupancy', 'name' => 'min_occupancy', 'type' => 'number', 'min' => 1, 'value' => $filters['min_occupancy'] ?? ''],
            ['label' => 'Sort', 'name' => 'sort', 'type' => 'select', 'options' => [
                ['label' => 'Name (A-Z)', 'value' => 'name_asc'],
                ['label' => 'Name (Z-A)', 'value' => 'name_desc'],
                ['label' => 'Price (Low-High)', 'value' => 'price_asc'],
                ['label' => 'Price (High-Low)', 'value' => 'price_desc'],
            ], 'value' => $filters['sort'] ?? 'name_asc'],
        ]"
        :reset-href="route('hotels.index')"
        apply-label="Apply"
        grid="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4"
    />

    @if($hotels->isEmpty())
        <x-ui.empty-state
            title="No hotels match your filters"
            description="Try adjusting search or price/occupancy ranges."
            action-label="Clear filters"
            :action-href="route('hotels.index')"
        />
    @else
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach ($hotels as $hotel)
                @php
                    $cheapestRoom = $hotel->rooms->sortBy('price')->first();
                @endphp
                <x-cards.hotel :hotel="$hotel" :cheapest-room="$cheapestRoom" :view-url="route('hotels.show', $hotel)" />
            @endforeach
        </div>

        <x-ui.pagination :paginator="$hotels" />
    @endif
</main>
@endsection
