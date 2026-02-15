@extends('layouts.app')

@section('title', 'Beach Events - Horror-Bark')

@section('content')
<main class="space-y-6">
    <x-ui.section-heading title="Beach Events" subtitle="Eerie tides, moonlit concerts, and chilling fun." align="center" size="xl" />

    <x-ui.alert-stack />

    <x-filters.panel
        :fields="[
            ['label' => 'Search', 'name' => 'search', 'type' => 'text', 'value' => $filters['search'] ?? '', 'placeholder' => 'Event or organizer', 'class' => 'lg:col-span-2'],
            ['label' => 'Date From', 'name' => 'date_from', 'type' => 'date', 'value' => $filters['date_from'] ?? ''],
            ['label' => 'Date To', 'name' => 'date_to', 'type' => 'date', 'value' => $filters['date_to'] ?? ''],
            ['label' => 'Min Price', 'name' => 'min_price', 'type' => 'number', 'min' => 0, 'step' => '0.01', 'value' => $filters['min_price'] ?? ''],
            ['label' => 'Max Price', 'name' => 'max_price', 'type' => 'number', 'min' => 0, 'step' => '0.01', 'value' => $filters['max_price'] ?? ''],
            ['label' => 'Sort', 'name' => 'sort', 'type' => 'select', 'options' => [
                ['label' => 'Date (Soonest)', 'value' => 'date_asc'],
                ['label' => 'Date (Latest)', 'value' => 'date_desc'],
                ['label' => 'Price (Low-High)', 'value' => 'price_asc'],
                ['label' => 'Price (High-Low)', 'value' => 'price_desc'],
                ['label' => 'Name (A-Z)', 'value' => 'name_asc'],
                ['label' => 'Name (Z-A)', 'value' => 'name_desc'],
            ], 'value' => $filters['sort'] ?? 'date_asc'],
        ]"
        :reset-href="route('beach-events.index')"
        apply-label="Apply"
        grid="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4"
    />

    @if ($beachEvents->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($beachEvents as $event)
                <x-cards.beach-event :event="$event" />
            @endforeach
        </div>

        <x-ui.pagination :paginator="$beachEvents" />
    @else
        <x-ui.empty-state title="No beach events match your filters" description="Try broadening date or price filters." />
    @endif
</main>
@endsection
