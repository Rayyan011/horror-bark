@extends('layouts.app')

@section('title', 'Ferry Tickets - Horror-Bark Theme Park')

@section('content')
<main class="space-y-6">
    <x-ui.section-heading title="Available Ferry Tickets" size="xl" />

    <x-ui.alert-stack />

    <x-filters.panel
        :fields="[
            ['label' => 'Search', 'name' => 'search', 'type' => 'text', 'value' => $filters['search'] ?? '', 'placeholder' => 'Ferry name', 'class' => 'lg:col-span-2'],
            ['label' => 'Destination', 'name' => 'island_id', 'type' => 'select', 'options' => collect($islands)->map(fn($island) => ['label' => $island->name, 'value' => $island->id])->prepend(['label' => 'All', 'value' => ''])->values()->all(), 'value' => $filters['island_id'] ?? ''],
            [
                'label' => 'Fare Range',
                'type' => 'range_pair',
                'min_name' => 'min_price',
                'max_name' => 'max_price',
                'min_value' => $filters['min_price'] ?? $filterBounds['price']['min'],
                'max_value' => $filters['max_price'] ?? $filterBounds['price']['max'],
                'min' => $filterBounds['price']['min'],
                'max' => $filterBounds['price']['max'],
                'step' => $filterBounds['price']['step'],
                'prefix' => 'MVR ',
                'class' => 'lg:col-span-2',
            ],
            [
                'label' => 'Minimum Capacity',
                'name' => 'min_capacity',
                'type' => 'range',
                'value' => $filters['min_capacity'] ?? $filterBounds['capacity']['min'],
                'min' => $filterBounds['capacity']['min'],
                'max' => $filterBounds['capacity']['max'],
                'step' => $filterBounds['capacity']['step'],
                'suffix' => ' seats',
            ],
            ['label' => 'Sort', 'name' => 'sort', 'type' => 'select', 'options' => [
                ['label' => 'Name (A-Z)', 'value' => 'name_asc'],
                ['label' => 'Name (Z-A)', 'value' => 'name_desc'],
                ['label' => 'Price (Low-High)', 'value' => 'price_asc'],
                ['label' => 'Price (High-Low)', 'value' => 'price_desc'],
            ], 'value' => $filters['sort'] ?? 'name_asc'],
        ]"
        :reset-href="route('ferries.index')"
        apply-label="Filter Ferries"
        grid="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4"
    />

    @if($ferries->isEmpty())
        <x-ui.empty-state
            title="No ferry tickets match your filters"
            description="Try adjusting search, island, or capacity filters."
            action-label="Clear filters"
            :action-href="route('ferries.index')"
        />
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($ferries as $ferry)
                <x-cards.ferry :ferry="$ferry" :booking-config="[
                    'mode' => 'datetime',
                    'rulesHint' => 'Whole hour between 9:00 and 16:00.',
                    'submitLabel' => 'Book ferry',
                ]" />
            @endforeach
        </div>

        <x-ui.pagination :paginator="$ferries" />
    @endif
</main>
@endsection
