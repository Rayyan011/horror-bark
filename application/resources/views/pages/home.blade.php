@extends('layouts.app')

@section('title', 'Home - Horror-Bark Theme Park')

@section('hero')
    <x-ui.hero-banner
        :image="asset('images/banner.webp')"
        title="Welcome to Horror-Bark"
        subtitle="A sanctuary of shadows where moonlight dances on cold stone and nightmares are draped in velvet."
    >
        <x-ui.button :href="route('hotels.index')" size="lg">View Chambers</x-ui.button>
        <x-ui.button :href="route('themepark.index')" variant="secondary" size="lg">Explore Grounds</x-ui.button>
    </x-ui.hero-banner>
@endsection

@section('content')
    @php
        $featuredRide = $rides->isNotEmpty() ? $rides->random() : null;
        $featuredBeachEvent = $beachEvents->isNotEmpty() ? $beachEvents->random() : null;
        $featuredHotel = $hotels->isNotEmpty() ? $hotels->random() : null;
    @endphp

    <section class="relative z-20 -mt-20 mb-16">
        <x-home.notice-cta message="The gates only part for those with reservations. Secure your passage before the moon reaches its zenith.">
            @livewire('book-now')
        </x-home.notice-cta>
    </section>

    <div class="divider-iron px-4 opacity-60">
        <span class="material-symbols-outlined divider-icon">church</span>
    </div>

    <x-home.featured-grid>
        @if ($featuredHotel)
            <x-featured-card
                :title="$featuredHotel->name"
                :description="$featuredHotel->description ?? 'Stone walls, whispered secrets, and suites draped in moonlit velvet.'"
                :images="$featuredHotel->images"
                :image="empty($featuredHotel->images) ? 'https://picsum.photos/seed/' . $featuredHotel->id . '/800/1000' : null"
                :link="route('hotels.index')"
                link-text="View Chambers"
            />
        @endif

        @if ($featuredRide)
            <x-featured-card
                :title="$featuredRide->name"
                :description="$featuredRide->description ?? 'Rides forged from twisted iron and fog. The laughter may not be your own.'"
                :images="$featuredRide->images"
                :image="empty($featuredRide->images) ? 'https://picsum.photos/seed/' . $featuredRide->id . '/800/1000' : null"
                :link="route('themepark.index')"
                link-text="Acquire Ticket"
            />
        @endif

        @if ($featuredBeachEvent)
            <x-featured-card
                :title="$featuredBeachEvent->name"
                :description="$featuredBeachEvent->description ?? 'Midnight gatherings where black ocean meets gray sand and distant music.'"
                :images="$featuredBeachEvent->images"
                :image="empty($featuredBeachEvent->images) ? 'https://picsum.photos/seed/' . $featuredBeachEvent->id . '/800/1000' : null"
                :link="route('beach-events.index')"
                link-text="Observe Events"
            />
        @endif
    </x-home.featured-grid>

    <div class="divider-iron px-4 opacity-60">
        <span class="material-symbols-outlined divider-icon">nightlight_round</span>
    </div>

    <section class="relative -mx-4 overflow-hidden border-y border-primary-light/20 bg-background-dark py-16 sm:-mx-6 lg:-mx-8">
        <div class="pointer-events-none absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/black-linen.png')] opacity-10"></div>
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-r from-background-dark via-primary-dark/30 to-background-dark"></div>

        <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-10 flex flex-col items-start justify-between gap-6 md:flex-row md:items-end">
                <div>
                    <h2 class="mb-3 font-display text-3xl tracking-wide text-moonlight drop-shadow-lg md:text-4xl">Island Cartography</h2>
                    <p class="max-w-2xl font-serif text-base leading-relaxed text-primary-light">
                        Navigate fog-laden paths and mark each destination before the dark tide shifts.
                    </p>
                </div>

                <x-ui.button :href="route('themepark.index')" variant="secondary">
                    Expand Map
                </x-ui.button>
            </div>

            <x-map.island-map
                :center="[4.22700104517645, 73.42662978621766]"
                :zoom="16"
                :interactive="false"
                height="h-[500px]"
                :markers="[
                    ...$hotels->map(fn ($h) => ['lat' => $h->latitude, 'lng' => $h->longitude, 'info' => $h->name, 'icon' => 'images/hotel.png'])->toArray(),
                    ...$rides->map(fn ($ride) => ['lat' => $ride->latitude, 'lng' => $ride->longitude, 'info' => $ride->name, 'icon' => 'images/ride.png'])->toArray(),
                    ...$games->map(fn ($game) => ['lat' => $game->latitude, 'lng' => $game->longitude, 'info' => $game->name, 'icon' => 'images/game.png'])->toArray(),
                    ...$beachEvents->map(fn ($event) => ['lat' => $event->latitude, 'lng' => $event->longitude, 'info' => $event->name, 'icon' => 'images/beach.png'])->toArray(),
                ]"
            />
        </div>
    </section>

    <x-home.attractions-grid :cards="$otherHaunts" />
@endsection
