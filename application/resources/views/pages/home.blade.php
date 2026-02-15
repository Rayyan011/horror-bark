@extends('layouts.app')

@section('title', 'Home - Horror-Bark Theme Park')

@section('content')
    <x-home.notice-cta message="To enjoy any services, please book your hotel stay first!">
        @livewire('book-now')
    </x-home.notice-cta>

    <x-home.featured-grid>
        @if ($rides->count() > 0)
            @php $featuredRide = $rides->random(); @endphp
            <x-featured-card
                :title="$featuredRide->name"
                :description="$featuredRide->description ?? 'Experience the thrill of this exciting ride at Horror-Bark Theme Park!'"
                :images="$featuredRide->images"
                :image="count($featuredRide->images) === 0 ? 'https://picsum.photos/seed/' . $featuredRide->id . '/400/300' : null"
                :link="route('themepark.index')"
                link-text="Book Now"
            />
        @endif

        @if ($beachEvents->count() > 0)
            @php $featuredBeachEvent = $beachEvents->random(); @endphp
            <x-featured-card
                :title="$featuredBeachEvent->name"
                :description="$featuredBeachEvent->description ?? 'Join eerie beach events on our main island.'"
                :images="$featuredBeachEvent->images"
                :image="count($featuredBeachEvent->images) === 0 ? 'https://picsum.photos/seed/' . $featuredBeachEvent->id . '/400/300' : null"
                :link="route('beach-events.index')"
                link-text="Book Now"
            />
        @endif

        @if ($hotels->count() > 0)
            @php $featuredHotel = $hotels->random(); @endphp
            <x-featured-card
                :title="$featuredHotel->name"
                :description="$featuredHotel->description ?? 'Book your stay on the island and unlock exclusive access.'"
                :images="$featuredHotel->images"
                :image="count($featuredHotel->images) === 0 ? 'https://picsum.photos/seed/' . $featuredHotel->id . '/400/300' : null"
                :link="route('hotels.index')"
                link-text="Book Now"
            />
        @endif
    </x-home.featured-grid>

    <section class="mb-12 mt-12">
        <x-ui.section-heading title="Explore the Island" size="md" class="mb-4" />

        <x-map.island-map
            :center="[4.22700104517645, 73.42662978621766]"
            :zoom="16"
            :interactive="false"
            :markers="[
                ...$hotels->map(fn ($h) => ['lat' => $h->latitude, 'lng' => $h->longitude, 'info' => $h->name, 'icon' => 'images/hotel.png'])->toArray(),
                ...$rides->map(fn ($ride) => ['lat' => $ride->latitude, 'lng' => $ride->longitude, 'info' => $ride->name, 'icon' => 'images/ride.png'])->toArray(),
                ...$games->map(fn ($game) => ['lat' => $game->latitude, 'lng' => $game->longitude, 'info' => $game->name, 'icon' => 'images/game.png'])->toArray(),
                ...$beachEvents->map(fn ($event) => ['lat' => $event->latitude, 'lng' => $event->longitude, 'info' => $event->name, 'icon' => 'images/beach.png'])->toArray(),
            ]"
        />
    </section>

    <x-home.attractions-grid>
        @if ($rides->count() > 0)
            @php $featuredRide = $rides->random(); @endphp
            <x-featured-card
                :title="$featuredRide->name"
                :description="$featuredRide->description ?? 'Experience the thrill of this exciting ride at Horror-Bark Theme Park!'"
                :images="$featuredRide->images"
                :image="count($featuredRide->images) === 0 ? 'https://picsum.photos/seed/' . $featuredRide->id . '/400/300' : null"
                :link="route('themepark.index')"
                link-text="Book Now"
            />
        @endif

        @if ($rides->count() > 0)
            @php $featuredRide = $rides->random(); @endphp
            <x-featured-card
                :title="$featuredRide->name"
                :description="$featuredRide->description ?? 'Experience the thrill of this exciting ride at Horror-Bark Theme Park!'"
                :images="$featuredRide->images"
                :image="count($featuredRide->images) === 0 ? 'https://picsum.photos/seed/' . $featuredRide->id . '/400/300' : null"
                :link="route('themepark.index')"
                link-text="Book Now"
            />
        @endif
    </x-home.attractions-grid>
@endsection
