@extends('layouts.app')

@section('title', 'Theme Park - Horror-Bark')

@section('content')
<main class="space-y-10">
    <section class="rounded-[2rem] border border-primary-light/15 bg-[radial-gradient(circle_at_top,_rgba(156,107,255,0.18),_transparent_55%),linear-gradient(135deg,_rgba(12,11,22,0.98),_rgba(24,18,34,0.92))] px-6 py-10 shadow-[0_30px_80px_rgba(0,0,0,0.35)] sm:px-10">
        <div class="max-w-4xl space-y-4">
            <p class="text-xs uppercase tracking-[0.32em] text-primary-light/70">Shadow Park Registry</p>
            <h1 class="font-display text-4xl italic leading-none text-moonlight sm:text-5xl">Rides, games, and midnight attractions.</h1>
            <p class="max-w-3xl font-serif text-base leading-relaxed text-primary-light/80">
                The atlas now lives on the home page. Use this registry to sort the active attractions, compare prices and capacities,
                and book the experiences that fit your stay inside Horror-Bark.
            </p>
        </div>
    </section>

    <x-ui.alert-stack />

    <x-ui.section-heading
        title="Theme Park Manifest"
        subtitle="Use the atlas above to orient yourself, then narrow the active ride and game listings below."
        size="lg"
        align="center"
    />

    <x-filters.panel
        :fields="[
            ['label' => 'Search', 'name' => 'search', 'type' => 'text', 'value' => $filters['search'] ?? '', 'placeholder' => 'Ride or game', 'class' => 'lg:col-span-2'],
            ['label' => 'Attraction Type', 'name' => 'section', 'type' => 'select', 'options' => [
                ['label' => 'All Attractions', 'value' => 'all'],
                ['label' => 'Rides Only', 'value' => 'rides'],
                ['label' => 'Games Only', 'value' => 'games'],
            ], 'value' => $filters['section'] ?? 'all'],
            ['label' => 'Island Type', 'name' => 'island_type', 'type' => 'select', 'options' => $islandTypeOptions, 'value' => $filters['island_type'] ?? ''],
            [
                'label' => 'Ticket Range',
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
                'suffix' => ' guests',
            ],
            ['label' => 'Sort', 'name' => 'sort', 'type' => 'select', 'options' => [
                ['label' => 'Name (A-Z)', 'value' => 'name_asc'],
                ['label' => 'Name (Z-A)', 'value' => 'name_desc'],
                ['label' => 'Price (Low-High)', 'value' => 'price_asc'],
                ['label' => 'Price (High-Low)', 'value' => 'price_desc'],
            ], 'value' => $filters['sort'] ?? 'name_asc'],
        ]"
        :reset-href="route('themepark.index')"
        apply-label="Filter Attractions"
        grid="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4"
    />

    <section class="space-y-4">
        <x-ui.section-heading title="Active Attractions" subtitle="Rides and games now live in one registry. Use the filter bar to narrow the set instead of switching sections." size="lg" />

        @if($activities->count() === 0)
            <x-ui.empty-state title="No attractions match your filters" />
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($activities as $activity)
                    <x-cards.activity
                        :item="$activity"
                        :type="$activity->catalog_type"
                        :booking-config="[
                            'mode' => 'datetime',
                            'rulesHint' => 'Only 9:00 or 17:00. Confirmed hotel stay required.',
                            'submitLabel' => $activity->catalog_type === 'game' ? 'Book game' : 'Book ride',
                        ]"
                    />
                @endforeach
            </div>

            <x-ui.pagination :paginator="$activities" />
        @endif
    </section>
</main>
@endsection
