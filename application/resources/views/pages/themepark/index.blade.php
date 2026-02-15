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

    <form method="GET" class="bg-gray-800 p-4 rounded border border-gray-700 mb-8 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
            <div class="lg:col-span-2">
                <label class="block text-sm mb-1" for="search">Search</label>
                <input id="search" name="search" type="text" value="{{ $filters['search'] ?? '' }}" placeholder="Ride or game"
                    class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white" />
            </div>
            <div>
                <label class="block text-sm mb-1" for="section">Section</label>
                <select id="section" name="section" class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white">
                    <option value="all" @selected(($filters['section'] ?? 'all') === 'all')>All</option>
                    <option value="rides" @selected(($filters['section'] ?? '') === 'rides')>Rides</option>
                    <option value="games" @selected(($filters['section'] ?? '') === 'games')>Games</option>
                </select>
            </div>
            <div>
                <label class="block text-sm mb-1" for="min_price">Min Price</label>
                <input id="min_price" name="min_price" type="number" min="0" step="0.01" value="{{ $filters['min_price'] ?? '' }}"
                    class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white" />
            </div>
            <div>
                <label class="block text-sm mb-1" for="max_price">Max Price</label>
                <input id="max_price" name="max_price" type="number" min="0" step="0.01" value="{{ $filters['max_price'] ?? '' }}"
                    class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white" />
            </div>
            <div>
                <label class="block text-sm mb-1" for="min_capacity">Min Capacity</label>
                <input id="min_capacity" name="min_capacity" type="number" min="1" value="{{ $filters['min_capacity'] ?? '' }}"
                    class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white" />
            </div>
            <div>
                <label class="block text-sm mb-1" for="sort">Sort</label>
                <select id="sort" name="sort" class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white">
                    <option value="name_asc" @selected(($filters['sort'] ?? 'name_asc') === 'name_asc')>Name (A-Z)</option>
                    <option value="name_desc" @selected(($filters['sort'] ?? '') === 'name_desc')>Name (Z-A)</option>
                    <option value="price_asc" @selected(($filters['sort'] ?? '') === 'price_asc')>Price (Low-High)</option>
                    <option value="price_desc" @selected(($filters['sort'] ?? '') === 'price_desc')>Price (High-Low)</option>
                </select>
            </div>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Apply</button>
            <a href="{{ route('themepark.index') }}" class="px-4 py-2 rounded border border-gray-600 text-gray-200 hover:text-white">Reset</a>
        </div>
    </form>

    @if(($filters['section'] ?? 'all') !== 'games')
        <section class="mb-12">
            <h2 class="text-3xl font-bold mb-6 horror-font">Rides</h2>
            @if($rides->isEmpty())
                <p class="text-gray-400">No rides match your filters.</p>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($rides as $ride)
                        <div class="bg-gray-800 p-6 rounded shadow border border-gray-700 flex flex-col justify-between">
                            <div>
                                <h3 class="text-2xl font-bold mb-2 text-white">{{ $ride->name }}</h3>
                                @if (!empty($ride->images))
                                    <x-image-carousel :images="$ride->images" :title="$ride->name" />
                                @else
                                    <img src="https://picsum.photos/seed/{{ $ride->id }}/400/300" alt="{{ $ride->name }} image" class="w-full h-48 object-cover" />
                                @endif
                                <p class="text-gray-400 mb-1">Island: {{ $ride->island->name ?? 'Horror Island' }}</p>
                                <p class="text-gray-400 mb-1">Price: MVR {{ number_format($ride->price, 2) }}</p>
                                <p class="text-gray-400 mb-1">Max Capacity: {{ $ride->max_capacity ?? 'N/A' }}</p>
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

                <div class="mt-6">
                    {{ $rides->links() }}
                </div>
            @endif
        </section>
    @endif

    @if(($filters['section'] ?? 'all') !== 'rides')
        <section>
            <h2 class="text-3xl font-bold mb-6 horror-font">Games</h2>
            @if($games->isEmpty())
                <p class="text-gray-400">No games match your filters.</p>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($games as $game)
                         <div class="bg-gray-800 p-6 rounded shadow border border-gray-700 flex flex-col justify-between">
                            <div>
                                <h3 class="text-2xl font-bold mb-2 text-white">{{ $game->name }}</h3>
                                @if (!empty($game->images))
                                    <x-image-carousel :images="$game->images" :title="$game->name" />
                                @else
                                    <img src="https://picsum.photos/seed/{{ $game->id }}/400/300" alt="{{ $game->name }} image" class="w-full h-48 object-cover" />
                                @endif
                                <p class="text-gray-400 mb-1">Island: {{ $game->island->name ?? 'Horror Island' }}</p>
                                <p class="text-gray-400 mb-1">Price: MVR {{ number_format($game->price, 2) }}</p>
                                <p class="text-gray-400 mb-1">Max Players per Booking: {{ $game->max_booking_quantity ?? 'N/A' }}</p>
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

                <div class="mt-6">
                    {{ $games->links() }}
                </div>
            @endif
        </section>
    @endif

</main>
@endsection
