@extends('layouts.app')

@section('title', 'Theme Park - Horror-Bark')

@section('content')
<main class="container mx-auto my-8 px-4">
    <h1 class="text-4xl font-bold mb-6 horror-font text-center">Welcome to the Theme Park!</h1>
    <p class="text-lg text-gray-300 mb-12 text-center">Discover thrilling rides and exciting games scattered across the island.</p>

    @if (session('status'))
        <div class="mb-4 text-green-300 text-sm">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 text-red-300 text-sm">{{ $errors->first() }}</div>
    @endif

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
                        <div class="mt-4">
                            @auth
                                <form method="POST" action="{{ route('bookings.rides.store', $ride) }}" class="space-y-3">
                                    @csrf
                                    <div>
                                        <label class="block text-sm mb-1" for="ride_booking_time_{{ $ride->id }}">Booking time</label>
                                        <input id="ride_booking_time_{{ $ride->id }}" name="booking_time" type="datetime-local" required
                                            class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white" />
                                        <p class="text-xs text-gray-400 mt-1">Only 9:00 or 17:00.</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm mb-1" for="ride_quantity_{{ $ride->id }}">Tickets</label>
                                        <input id="ride_quantity_{{ $ride->id }}" name="quantity" type="number" min="1" max="{{ $ride->max_booking_quantity }}" value="1" required
                                            class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white" />
                                    </div>
                                    <button type="submit" class="w-full bg-red-600 text-white py-2 px-4 rounded hover:bg-red-700">
                                        Book ride
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="inline-block w-full text-center bg-red-600 text-white py-2 px-4 rounded hover:bg-red-700">
                                    Log in to book
                                </a>
                            @endauth
                        </div>
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
                        <div class="mt-4">
                            @auth
                                <form method="POST" action="{{ route('bookings.games.store', $game) }}" class="space-y-3">
                                    @csrf
                                    <div>
                                        <label class="block text-sm mb-1" for="game_booking_time_{{ $game->id }}">Booking time</label>
                                        <input id="game_booking_time_{{ $game->id }}" name="booking_time" type="datetime-local" required
                                            class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white" />
                                        <p class="text-xs text-gray-400 mt-1">Only 9:00 or 17:00.</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm mb-1" for="game_quantity_{{ $game->id }}">Players</label>
                                        <input id="game_quantity_{{ $game->id }}" name="quantity" type="number" min="1" max="{{ $game->max_booking_quantity }}" value="1" required
                                            class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white" />
                                    </div>
                                    <button type="submit" class="w-full bg-yellow-600 text-white py-2 px-4 rounded hover:bg-yellow-700">
                                        Book game
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="inline-block w-full text-center bg-yellow-600 text-white py-2 px-4 rounded hover:bg-yellow-700">
                                    Log in to book
                                </a>
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

</main>
@endsection