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
            ['label' => 'Min Price', 'name' => 'min_price', 'type' => 'number', 'min' => 0, 'step' => '0.01', 'value' => $filters['min_price'] ?? ''],
            ['label' => 'Max Price', 'name' => 'max_price', 'type' => 'number', 'min' => 0, 'step' => '0.01', 'value' => $filters['max_price'] ?? ''],
            ['label' => 'Min Capacity', 'name' => 'min_capacity', 'type' => 'number', 'min' => 1, 'value' => $filters['min_capacity'] ?? ''],
            ['label' => 'Sort', 'name' => 'sort', 'type' => 'select', 'options' => [
                ['label' => 'Name (A-Z)', 'value' => 'name_asc'],
                ['label' => 'Name (Z-A)', 'value' => 'name_desc'],
                ['label' => 'Price (Low-High)', 'value' => 'price_asc'],
                ['label' => 'Price (High-Low)', 'value' => 'price_desc'],
            ], 'value' => $filters['sort'] ?? 'name_asc'],
        ]"
        :reset-href="route('ferries.index')"
        apply-label="Apply"
        grid="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4"
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
