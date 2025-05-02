@extends('layouts.app')

@section('title', 'Theme Park - Horror-Bark')

@section('content')
<main class="container mx-auto my-8 px-4">
    <h1 class="text-4xl font-bold mb-6 horror-font text-center">Welcome to the Theme Park!</h1>
    <p class="text-lg text-gray-300 mb-12 text-center">Discover thrilling rides and exciting games scattered across the island.</p>

    {{-- Rides Section --}}
    <section class="mb-12">
        <h2 class="text-3xl font-bold mb-6 horror-font">Rides</h2>
        @if($rides->isEmpty())
            <p class="text-gray-400">No rides available at the moment. Check back soon!</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($rides as $ride)
                    <div class="bg-gray-800 p-6 rounded shadow border border-gray-700 flex flex-col justify-between">
                        <div>
                            <h3 class="text-2xl font-bold mb-2 text-white">{{ $ride->name }}</h3>
                            {{-- Display Ride Image if available --}}
                            @if (!empty($ride->images))
                                <x-image-carousel :images="$ride->images" :title="$ride->name" />
                            @else
                                <img src="https://picsum.photos/seed/{{ $ride->id }}/400/300" alt="{{ $ride->name }} image" class="w-full h-48 object-cover" />
                            @endif
                            <p class="text-gray-400 mb-1">Price: ${{ number_format($ride->price, 2) }}</p>
                            <p class="text-gray-400 mb-1">Max Capacity: {{ $ride->max_capacity ?? 'N/A' }}</p>
                            {{-- Add Description if available in your Ride model --}}
                            <p class="text-gray-300 mt-2">{{ $ride->description ?? 'Experience the thrill!' }}</p>
                        </div>
                        <a href="#" class="inline-block text-center mt-4 bg-red-600 text-white py-2 px-4 rounded hover:bg-red-700">
                            Ride Details (Coming Soon)
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    {{-- Games Section --}}
    <section>
        <h2 class="text-3xl font-bold mb-6 horror-font">Games</h2>
        @if($games->isEmpty())
            <p class="text-gray-400">No games available right now.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($games as $game)
                     <div class="bg-gray-800 p-6 rounded shadow border border-gray-700 flex flex-col justify-between">
                        <div>
                            <h3 class="text-2xl font-bold mb-2 text-white">{{ $game->name }}</h3>
                            {{-- Display Game Image if available --}}
                            @if (!empty($game->images))
                                <x-image-carousel :images="$game->images" :title="$game->name" />
                            @else
                                <img src="https://picsum.photos/seed/{{ $game->id }}/400/300" alt="{{ $game->name }} image" class="w-full h-48 object-cover" />
                            @endif
                            <p class="text-gray-400 mb-1">Price: ${{ number_format($game->price, 2) }}</p>
                            <p class="text-gray-400 mb-1">Max Players per Booking: {{ $game->max_booking_quantity ?? 'N/A' }}</p>
                            {{-- Add Description if available in your Game model --}}
                            <p class="text-gray-300 mt-2">{{ $game->description ?? 'Test your skills!' }}</p>
                        </div>
                        <a href="#" class="inline-block text-center mt-4 bg-yellow-600 text-white py-2 px-4 rounded hover:bg-yellow-700">
                            Game Info (Coming Soon)
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

</main>
@endsection