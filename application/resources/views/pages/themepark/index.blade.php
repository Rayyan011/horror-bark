@extends('layouts.app')

@section('title', 'Theme Park - Horror-Bark')

@section('content')
<main class="space-y-8">
    <x-ui.section-heading title="Welcome to the Theme Park!" subtitle="Discover thrilling rides and exciting games scattered across the island." size="xl" align="center" />

    <x-ui.alert-stack />

    <x-filters.panel
        :fields="[
            ['label' => 'Search', 'name' => 'search', 'type' => 'text', 'value' => $filters['search'] ?? '', 'placeholder' => 'Ride or game', 'class' => 'lg:col-span-2'],
            ['label' => 'Section', 'name' => 'section', 'type' => 'select', 'options' => [
                ['label' => 'All', 'value' => 'all'],
                ['label' => 'Rides', 'value' => 'rides'],
                ['label' => 'Games', 'value' => 'games'],
            ], 'value' => $filters['section'] ?? 'all'],
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
        :reset-href="route('themepark.index')"
        apply-label="Apply"
        grid="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4"
    />

    @if(($filters['section'] ?? 'all') !== 'games')
        <section class="space-y-4">
            <x-ui.section-heading title="Rides" size="lg" />

            @if($rides->isEmpty())
                <x-ui.empty-state title="No rides match your filters" />
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($rides as $ride)
                        <x-cards.activity
                            :item="$ride"
                            type="ride"
                            :booking-config="[
                                'mode' => 'datetime',
                                'rulesHint' => 'Only 9:00 or 17:00.',
                                'submitLabel' => 'Book ride',
                            ]"
                        />
                    @endforeach
                </div>

                <x-ui.pagination :paginator="$rides" />
            @endif
        </section>
    @endif

    @if(($filters['section'] ?? 'all') !== 'rides')
        <section class="space-y-4">
            <x-ui.section-heading title="Games" size="lg" />

            @if($games->isEmpty())
                <x-ui.empty-state title="No games match your filters" />
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($games as $game)
                        <x-cards.activity
                            :item="$game"
                            type="game"
                            :booking-config="[
                                'mode' => 'datetime',
                                'rulesHint' => 'Only 9:00 or 17:00.',
                                'submitLabel' => 'Book game',
                            ]"
                        />
                    @endforeach
                </div>

                <x-ui.pagination :paginator="$games" />
            @endif
        </section>
    @endif
</main>
@endsection
