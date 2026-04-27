@extends('layouts.app')

@section('title', 'Home - Horror-Bark Theme Park')

@php
    $heroImage = \App\Support\HorrorBarkThemeAssets::homeHero();
    $allRooms = $hotels->flatMap(fn ($hotel) => $hotel->rooms)->values();
    $startingRoom = $allRooms->sortBy('price')->first();
    $firstPromotion = $promotions->first();
    $heroStats = collect([
        [
            'label' => 'Chambers From',
            'value' => $startingRoom ? 'MVR ' . number_format($startingRoom->price, 2) : 'By arrangement',
            'meta' => $startingRoom ? $startingRoom->hotel?->name : 'Tonight\'s quietest quarter',
        ],
        [
            'label' => 'Blackwater Crossings',
            'value' => $ferries->count(),
            'meta' => \Illuminate\Support\Str::plural('night ferry', $ferries->count()) . ' currently listed',
        ],
        [
            'label' => 'Midnight Attractions',
            'value' => $rides->count() + $games->count() + $beachEvents->count(),
            'meta' => 'Rides, games, and shoreline rituals in one registry',
        ],
    ]);

    $districts = collect([
        [
            'name' => 'Blackwater Approach',
            'eyebrow' => 'Arrival Passage',
            'summary' => 'Arrive by lantern-lit ferry before the island opens. The crossing sets the pace for the night before guests move inland.',
            'detail' => $ferries->sortBy('price')->first()?->name ? 'Featured crossing: ' . $ferries->sortBy('price')->first()->name : 'Lantern barges and harbor bells wait at the quay.',
            'stat' => $ferries->count() . ' ' . \Illuminate\Support\Str::plural('crossing', $ferries->count()),
            'href' => route('ferries.index'),
            'cta' => 'Chart Passage',
            'x' => '16%',
            'y' => '72%',
            'tone' => 'harbor',
        ],
        [
            'name' => 'Manor Ward',
            'eyebrow' => 'Chamber Quarter',
            'summary' => 'Reserve rooms among stone corridors, gallery windows, and quiet harbor-facing chambers prepared for overnight guests.',
            'detail' => $startingRoom ? 'Starting with ' . $startingRoom->room_number . ' at ' . $startingRoom->hotel?->name : 'Stone corridors, velvet galleries, and candlelit suites.',
            'stat' => $hotels->count() . ' ' . \Illuminate\Support\Str::plural('hotel', $hotels->count()),
            'href' => route('hotels.index'),
            'cta' => 'Reserve Chambers',
            'x' => '34%',
            'y' => '26%',
            'tone' => 'manor',
        ],
        [
            'name' => 'Shadow Park',
            'eyebrow' => 'Midway Grounds',
            'summary' => 'Enter the ride grounds and game stalls after check-in, with timed sessions and ticketed attractions grouped together.',
            'detail' => $rides->first()?->name ? 'Opening with ' . $rides->first()->name : 'Twisted iron, ceremonial timing, and midnight trials.',
            'stat' => ($rides->count() + $games->count()) . ' active attractions',
            'href' => route('themepark.index'),
            'cta' => 'Enter Grounds',
            'x' => '58%',
            'y' => '52%',
            'tone' => 'midway',
        ],
        [
            'name' => 'Pale Moon Strand',
            'eyebrow' => 'Shoreline Gatherings',
            'summary' => 'End the route at the shore, where bonfires, vigils, and late gatherings are listed by date and capacity.',
            'detail' => $beachEvents->sortBy('event_date')->first()?->name ? 'Tonight\'s invitation: ' . $beachEvents->sortBy('event_date')->first()->name : 'Ceremonial fires, surf, and silver table settings.',
            'stat' => $beachEvents->count() . ' moonlit events',
            'href' => route('beach-events.index'),
            'cta' => 'View Shoreline',
            'x' => '82%',
            'y' => '68%',
            'tone' => 'shore',
        ],
    ]);
@endphp

@section('hero')
    <section class="home-hero-shell">
        <div class="home-hero-media">
            <img
                src="{{ $heroImage }}"
                alt="Horror-Bark harbor arrival"
                class="h-full w-full object-cover"
            />
        </div>
        <div class="home-hero-overlay"></div>
        <div class="home-hero-noise"></div>

        <div class="relative z-10 mx-auto w-full max-w-7xl px-4 py-20 sm:px-6 lg:px-8 lg:py-24">
            <div class="max-w-4xl space-y-6">
                <p class="home-hero-kicker">Horror-Bark Arrival Ledger</p>
                <h1 class="home-hero-title">Arrive By Lantern. Book By Bell.</h1>
                <p class="readable-copy max-w-3xl text-lg !leading-9">
                    Horror-Bark now leads guests through a clearer customer journey: reserve chambers, chart blackwater passage,
                    claim the live promotions, and step into the island without the homepage fighting the rest of the theme.
                </p>

                <div class="flex flex-wrap gap-3 pt-2">
                    <x-ui.button :href="route('hotels.index')" size="lg">Reserve Chambers</x-ui.button>
                    <x-ui.button
                        :href="$firstPromotion ? route('promotions.show', $firstPromotion) : route('themepark.index')"
                        variant="secondary"
                        size="lg"
                    >
                        {{ $firstPromotion ? 'Open Live Promotions' : 'Explore Grounds' }}
                    </x-ui.button>
                </div>
            </div>

            <div class="mt-10 grid gap-4 md:grid-cols-3">
                @foreach ($heroStats as $stat)
                    <article class="home-hero-stat">
                        <p class="theme-kicker">{{ $stat['label'] }}</p>
                        <p class="home-hero-stat-value">{{ $stat['value'] }}</p>
                        <p class="readable-muted">{{ $stat['meta'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endsection

@section('content')
    <main class="space-y-14">
        <section class="grid gap-5 lg:grid-cols-[1.15fr,0.85fr]">
            <x-ui.surface class="space-y-4 px-6 py-6 sm:px-8">
                <p class="theme-kicker">Tonight's Protocol</p>
                <h2 class="font-display text-3xl italic text-moonlight sm:text-4xl">One path through the island.</h2>
                <p class="readable-copy">
                    Begin with live offers, choose a district, then browse the broader night registry from a single route through the island.
                </p>
            </x-ui.surface>

            <x-ui.surface class="space-y-4 px-6 py-6 sm:px-8">
                <p class="theme-kicker">Live Discounts</p>
                <h2 class="font-display text-3xl italic text-moonlight sm:text-4xl">{{ $promotions->count() }} discounts across the island.</h2>
                <p class="readable-copy">
                    Active promotions lead directly to discounted rooms, crossings, and events with the reduced rate preserved through checkout.
                </p>
            </x-ui.surface>
        </section>

        @if ($promotions->isNotEmpty())
            <section class="space-y-6">
                <x-home.featured-grid
                    title="Discounts Under The Pale Moon"
                    subtitle="Each card leads to real discounted inventory with the reduced rate carried into checkout."
                >
                    @foreach ($promotions as $promotion)
                        <x-featured-card
                            :title="filled($promotion->discount_percentage) ? $promotion->resolved_title . ' · ' . number_format((float) $promotion->discount_percentage, 0) . '% Off' : $promotion->resolved_title"
                            :description="$promotion->resolved_description"
                            :images="array_filter([$promotion->resolved_image_path])"
                            :link="route('promotions.show', $promotion)"
                            :link-text="$promotion->resolved_cta_label"
                        />
                    @endforeach
                </x-home.featured-grid>
            </section>
        @endif

        <section class="home-chart-shell">
            <div class="space-y-4">
                <p class="theme-kicker">Island Atlas</p>
                <h2 class="font-display text-4xl italic leading-none text-moonlight sm:text-5xl">A calmer route across the island.</h2>
                <p class="readable-copy max-w-3xl">
                    Trace the night by district: arrive through the harbor, settle into the manor, cross into the midway, then follow the path down to the shore.
                    Each marker opens the live listings for that part of Horror-Bark.
                </p>
            </div>

            <div class="grid gap-6 xl:grid-cols-[1.05fr,0.95fr]">
                <div class="home-chart-stage">
                    <div class="home-chart-route home-chart-route-a"></div>
                    <div class="home-chart-route home-chart-route-b"></div>
                    <div class="home-chart-route home-chart-route-c"></div>
                    <div class="home-chart-glow home-chart-glow-a"></div>
                    <div class="home-chart-glow home-chart-glow-b"></div>

                    @foreach ($districts as $district)
                        <a
                            href="{{ $district['href'] }}"
                            class="home-chart-node home-chart-node--{{ $district['tone'] }}"
                            style="left: {{ $district['x'] }}; top: {{ $district['y'] }};"
                        >
                            <span class="home-chart-node-ring"></span>
                            <span class="home-chart-node-title">{{ $district['name'] }}</span>
                            <span class="home-chart-node-meta">{{ $district['stat'] }}</span>
                        </a>
                    @endforeach
                </div>

                <div class="grid gap-4">
                    @foreach ($districts as $district)
                        <article class="home-district-card">
                            <div class="space-y-2">
                                <p class="theme-kicker">{{ $district['eyebrow'] }}</p>
                                <h3 class="font-display text-3xl italic text-moonlight">{{ $district['name'] }}</h3>
                                <p class="readable-copy !text-[1.02rem]">{{ $district['summary'] }}</p>
                            </div>

                            <div class="space-y-3">
                                <p class="readable-muted">{{ $district['detail'] }}</p>
                                <div class="flex flex-wrap items-center justify-between gap-3">
                                    <span class="catalog-range-pill">{{ $district['stat'] }}</span>
                                    <a href="{{ $district['href'] }}" class="gothic-link">{{ $district['cta'] }}</a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <x-home.attractions-grid
            title="Tonight's Registry"
            subtitle="The broader night catalog still lives here, but now it reads as a later chapter after offers and route-planning instead of duplicating the top of the page."
            :cards="$otherHaunts"
        />
    </main>
@endsection
