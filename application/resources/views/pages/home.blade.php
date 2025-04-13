@extends('layouts.app')

@section('title', 'Home - Horror-Bark Theme Park') {{-- Optional: Set page title --}}

@section('content') {{-- Start the content section --}}
    <!-- Hero Section -->
    <section class="bg-cover bg-center h-96" style="background-image: url('{{ asset('images/default-hero.jpg') }}');">
        <div class="bg-black bg-opacity-70 h-full flex items-center justify-center">
            <div class="text-center">
                <h2 class="text-5xl font-bold mb-4 horror-font">Welcome to Horror-Bark!</h2>
                <p class="text-xl">Experience the terror, the thrills, and the unknown.</p>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main class="container mx-auto my-8 px-4">
        <!-- Booking Requirement Notice -->
        <div class="bg-red-800 border border-red-900 text-red-300 p-4 rounded mb-8">
            <strong>Important:</strong> To enjoy any services, please book your hotel stay first!
        </div>

        <!-- Advertisements Section -->
        <section class="mb-12">
            <h3 class="text-2xl font-bold mb-4 horror-font">Featured Experiences</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Theme Park Advertisement -->
                @if ($rides->count() > 0)
                    @php $featuredRide = $rides->random(); @endphp

                    <x-featured-card
                        :title="$featuredRide->name"
                        :description="$featuredRide->description ?? 'Experience the thrill of this exciting ride at Horror-Bark Theme Park!'"
                        :images="$featuredRide->images"
                        :image="count($featuredRide->images) === 0 ? 'https://picsum.photos/seed/' . $featuredRide->id . '/400/300' : null"
                        :link="route('home', $featuredRide)"
                        link-text="More info"
                    />
                @endif
                <!-- Beach Sports Advertisement -->
                @if ($beachEvents->count() > 0)
                    @php $featuredBeachEvent = $beachEvents->random(); @endphp

                    <x-featured-card
                        :title="$featuredBeachEvent->name"
                        :description="$featuredBeachEvent->description ?? 'Join eerie beach events on our main island – from ghostly jet skiing to moonlit paddleboarding and mysterious concerts!'"
                        :images="$featuredBeachEvent->images"
                        :image="count($featuredBeachEvent->images) === 0 ? 'https://picsum.photos/seed/' . $featuredBeachEvent->id . '/400/300' : null"
                        :link="route('home', $featuredBeachEvent)" {{-- Adjust route as needed --}}
                        link-text="Discover More"
                    />
                @endif
                <!-- Hotels Advertisement -->
                @if ($hotels->count() > 0)
                    @php $featuredHotel = $hotels->random(); @endphp

                    <x-featured-card
                        :title="$featuredHotel->name"
                        :description="$featuredHotel->description ?? 'Book your stay on the island and unlock exclusive access to our spine-chilling experiences.'"
                        :images="$featuredHotel->images"
                        :image="count($featuredHotel->images) === 0 ? 'https://picsum.photos/seed/' . $featuredHotel->id . '/400/300' : null"
                        :link="route('home', $featuredHotel)" {{-- Adjust route as needed --}}
                        link-text="Book Now"
                    />
                @endif
            </div>
        </section>

        <!-- Map Section -->
        <section class="mb-12 h-96">
            <h3 class="text-2xl font-bold mb-4 horror-font">Explore the Island</h3>
            {{-- <div class="relative">
                <img src="https://source.unsplash.com/1200x600/?haunted,island" alt="Island Map"
                    class="w-full rounded shadow-lg" />
                <!-- Example Map Markers (positioned absolutely over the image) -->
                <div class="absolute top-10 left-20 bg-red-700 text-white px-2 py-1 rounded text-xs">Hotel</div>
                <div class="absolute top-20 left-1/2 bg-green-700 text-white px-2 py-1 rounded text-xs">Theme Park</div>
                <div class="absolute top-40 right-20 bg-blue-700 text-white px-2 py-1 rounded text-xs">Beach</div>
                <div class="absolute bottom-10 left-40 bg-yellow-700 text-white px-2 py-1 rounded text-xs">Restaurants</div>
            </div> --}}

                <x-maps-leaflet
                    :center-point="[4.22700104517645, 73.42662978621766]"
                    :zoom-level="16"
                    :zoom-control="false"
                    :scroll-wheel-zoom="false"
                    :markers="[
                        ...$hotels->map(fn ($h) => ['lat' => $h->latitude, 'lng' => $h->longitude, 'info' => $h->name, 'icon' => 'images/hotel.png'])->toArray(),
                        ...$rides->map(fn ($ride) => ['lat' => $ride->latitude, 'lng' => $ride->longitude, 'info' => $ride->name, 'icon' => 'images/ride.png'])->toArray(),
                        ...$games->map(fn ($game) => ['lat' => $game->latitude, 'lng' => $game->longitude, 'info' => $game->name, 'icon' => 'images/game.png'])->toArray(),
                        ...$beachEvents->map(fn ($event) => ['lat' => $event->latitude, 'lng' => $event->longitude, 'info' => $event->name, 'icon' => 'images/beach.png'])->toArray(),
                    ]"
                />
        </section>

        <!-- Additional Attractions Section -->
        <section>
            <h3 class="text-2xl font-bold mb-4 horror-font">Other Attractions at Horror-Bark</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-800 p-6 rounded shadow border border-gray-700">
                    <h4 class="text-xl font-semibold mb-2 horror-font">Superhero 4D Experience</h4>
                    <p class="text-gray-300">
                        Step into a world where nightmares come alive! Experience our 4D simulator and join the battle
                        against the unseen.
                    </p>
                </div>
                <div class="bg-gray-800 p-6 rounded shadow border border-gray-700">
                    <h4 class="text-xl font-semibold mb-2 horror-font">Haunted Maze</h4>
                    <p class="text-gray-300">
                        Dare to enter our twisted haunted maze. Beware—every corner hides a new terror!
                    </p>
                </div>
            </div>
        </section>
    </main>
@endsection {{-- End the content section --}}
