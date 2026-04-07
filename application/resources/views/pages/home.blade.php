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

    @if ($promotions->isNotEmpty())
        <section class="space-y-6">
            <x-ui.section-heading title="Promotions Under The Pale Moon" size="lg" />

            <x-home.featured-grid>
                @foreach ($promotions as $promotion)
                    <x-featured-card
                        :title="filled($promotion->discount_percentage) ? $promotion->title . ' · ' . number_format((float) $promotion->discount_percentage, 0) . '% Off' : $promotion->title"
                        :description="$promotion->description"
                        :images="array_filter([$promotion->image_path])"
                        :link="$promotion->cta_url"
                        :link-text="$promotion->resolved_cta_label"
                    />
                @endforeach
            </x-home.featured-grid>
        </section>

        <div class="divider-iron px-4 opacity-60">
            <span class="material-symbols-outlined divider-icon">local_activity</span>
        </div>
    @endif

    <x-home.featured-grid>
        @if ($featuredHotel)
            <x-featured-card
                :title="$featuredHotel->name"
                :description="$featuredHotel->description ?? 'Stone walls, whispered secrets, and suites draped in moonlit velvet.'"
                :images="$featuredHotel->images"
                :image="empty($featuredHotel->images) ? \App\Support\HorrorGeneratedMediaCatalog::path('fallbacks', 'hotel') : null"
                :link="route('hotels.index')"
                link-text="View Chambers"
            />
        @endif

        @if ($featuredRide)
            <x-featured-card
                :title="$featuredRide->name"
                :description="$featuredRide->description ?? 'Rides forged from twisted iron and fog. The laughter may not be your own.'"
                :images="$featuredRide->images"
                :image="empty($featuredRide->images) ? \App\Support\HorrorGeneratedMediaCatalog::path('fallbacks', 'ride') : null"
                :link="route('themepark.index')"
                link-text="Acquire Ticket"
            />
        @endif

        @if ($featuredBeachEvent)
            <x-featured-card
                :title="$featuredBeachEvent->name"
                :description="$featuredBeachEvent->description ?? 'Midnight gatherings where black ocean meets gray sand and distant music.'"
                :images="$featuredBeachEvent->images"
                :image="empty($featuredBeachEvent->images) ? \App\Support\HorrorGeneratedMediaCatalog::path('fallbacks', 'beach-event') : null"
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
                    Explore Grounds
                </x-ui.button>
            </div>

            <x-map.island-map
                :zones="$atlasData['zones']"
                :locations="$atlasData['locations']"
                :featured="$atlasData['featured']"
                compact
                height="min-h-[34rem]"
                subtitle="A fictional chart of Horror-Bark Isle, with live destinations surfaced directly from tonight’s catalog."
            />
        </div>
    </section>

    <x-home.attractions-grid :cards="$otherHaunts" />
@endsection
