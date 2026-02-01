@extends('layouts.app')

@section('title', 'My Bookings - Horror-Bark')

@section('content')
<main class="container mx-auto my-8 px-4">
    <h1 class="text-4xl font-bold mb-6 horror-font">My Bookings</h1>

    @if (session('status'))
        <div class="mb-4 text-green-300 text-sm">{{ session('status') }}</div>
    @endif

    <section class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-gray-800 p-4 rounded border border-gray-700">
            <p class="text-gray-400 text-sm">Total bookings</p>
            <p class="text-2xl font-semibold">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-gray-800 p-4 rounded border border-gray-700">
            <p class="text-gray-400 text-sm">Upcoming</p>
            <p class="text-2xl font-semibold">{{ $stats['upcoming'] }}</p>
        </div>
        <div class="bg-gray-800 p-4 rounded border border-gray-700">
            <p class="text-gray-400 text-sm">Total spent</p>
            <p class="text-2xl font-semibold">${{ number_format($stats['spent'], 2) }}</p>
        </div>
    </section>

    <form method="GET" class="bg-gray-800 p-4 rounded border border-gray-700 mb-8 grid grid-cols-1 md:grid-cols-5 gap-4">
        <div>
            <label class="block text-sm mb-1" for="type">Type</label>
            <select id="type" name="type" class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white">
                <option value="">All</option>
                <option value="hotel" @selected(($filters['type'] ?? '') === 'hotel')>Hotel</option>
                <option value="ferry" @selected(($filters['type'] ?? '') === 'ferry')>Ferry</option>
                <option value="ride" @selected(($filters['type'] ?? '') === 'ride')>Ride</option>
                <option value="game" @selected(($filters['type'] ?? '') === 'game')>Game</option>
                <option value="beach-event" @selected(($filters['type'] ?? '') === 'beach-event')>Beach Event</option>
            </select>
        </div>
        <div>
            <label class="block text-sm mb-1" for="status">Status</label>
            <select id="status" name="status" class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white">
                <option value="">Any</option>
                <option value="confirmed" @selected(($filters['status'] ?? '') === 'confirmed')>Confirmed</option>
                <option value="canceled" @selected(($filters['status'] ?? '') === 'canceled')>Canceled</option>
            </select>
        </div>
        <div>
            <label class="block text-sm mb-1" for="from">From</label>
            <input id="from" name="from" type="date" value="{{ $filters['from'] ?? '' }}"
                class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white" />
        </div>
        <div>
            <label class="block text-sm mb-1" for="to">To</label>
            <input id="to" name="to" type="date" value="{{ $filters['to'] ?? '' }}"
                class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white" />
        </div>
        <div>
            <label class="block text-sm mb-1" for="search">Search</label>
            <input id="search" name="search" type="text" value="{{ $filters['search'] ?? '' }}"
                placeholder="Search name or room"
                class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white" />
        </div>
        <div class="md:col-span-5 flex gap-2">
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Apply</button>
            <a href="{{ route('bookings.index') }}" class="px-4 py-2 rounded border border-gray-600 text-gray-200 hover:text-white">Reset</a>
        </div>
    </form>

    <section class="mb-10">
        <h2 class="text-2xl font-bold mb-4">Hotel Stays</h2>
        @forelse ($hotelBookings as $booking)
            <div class="bg-gray-800 p-4 rounded border border-gray-700 mb-4">
                <div class="flex flex-wrap justify-between gap-4">
                    <div>
                        <p class="text-white font-semibold">{{ $booking->room->hotel->name ?? 'Hotel' }}</p>
                        <p class="text-gray-400 text-sm">Room {{ $booking->room->room_number ?? '' }}</p>
                        <p class="text-gray-400 text-sm">Dates: {{ $booking->start_date }} → {{ $booking->end_date }}</p>
                        <p class="text-gray-400 text-sm">Guests: {{ $booking->quantity }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-300 text-sm">Total: ${{ number_format($booking->total_price, 2) }}</p>
                        <p class="text-gray-400 text-sm">Status: {{ ucfirst($booking->status) }}</p>
                        <a href="{{ route('bookings.hotels.show', $booking) }}" class="text-sm text-red-300 hover:text-red-200">View details</a>
                        @if ($booking->status !== 'canceled')
                            <form method="POST" action="{{ route('bookings.hotels.cancel', $booking) }}" class="mt-2">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="bg-red-700 text-white px-3 py-1 rounded hover:bg-red-600">
                                    Cancel
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p class="text-gray-400">No hotel bookings yet.</p>
        @endforelse
        {{ $hotelBookings->withQueryString()->links() }}
    </section>

    <section class="mb-10">
        <h2 class="text-2xl font-bold mb-4">Ferry Tickets</h2>
        @forelse ($ferryBookings as $booking)
            <div class="bg-gray-800 p-4 rounded border border-gray-700 mb-4">
                <div class="flex flex-wrap justify-between gap-4">
                    <div>
                        <p class="text-white font-semibold">{{ $booking->ferry->name ?? 'Ferry' }}</p>
                        <p class="text-gray-400 text-sm">Time: {{ $booking->booking_time }}</p>
                        <p class="text-gray-400 text-sm">Tickets: {{ $booking->quantity }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-300 text-sm">Total: ${{ number_format($booking->total_price, 2) }}</p>
                        <p class="text-gray-400 text-sm">Status: {{ ucfirst($booking->status) }}</p>
                        <a href="{{ route('bookings.ferries.show', $booking) }}" class="text-sm text-red-300 hover:text-red-200">View details</a>
                        @if ($booking->status !== 'canceled')
                            <form method="POST" action="{{ route('bookings.ferries.cancel', $booking) }}" class="mt-2">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="bg-red-700 text-white px-3 py-1 rounded hover:bg-red-600">
                                    Cancel
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p class="text-gray-400">No ferry bookings yet.</p>
        @endforelse
        {{ $ferryBookings->withQueryString()->links() }}
    </section>

    <section class="mb-10">
        <h2 class="text-2xl font-bold mb-4">Ride Bookings</h2>
        @forelse ($rideBookings as $booking)
            <div class="bg-gray-800 p-4 rounded border border-gray-700 mb-4">
                <div class="flex flex-wrap justify-between gap-4">
                    <div>
                        <p class="text-white font-semibold">{{ $booking->ride->name ?? 'Ride' }}</p>
                        <p class="text-gray-400 text-sm">Time: {{ $booking->booking_time }}</p>
                        <p class="text-gray-400 text-sm">Tickets: {{ $booking->quantity }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-300 text-sm">Total: ${{ number_format($booking->total_price, 2) }}</p>
                        <p class="text-gray-400 text-sm">Status: {{ ucfirst($booking->status) }}</p>
                        <a href="{{ route('bookings.rides.show', $booking) }}" class="text-sm text-red-300 hover:text-red-200">View details</a>
                        @if ($booking->status !== 'canceled')
                            <form method="POST" action="{{ route('bookings.rides.cancel', $booking) }}" class="mt-2">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="bg-red-700 text-white px-3 py-1 rounded hover:bg-red-600">
                                    Cancel
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p class="text-gray-400">No ride bookings yet.</p>
        @endforelse
        {{ $rideBookings->withQueryString()->links() }}
    </section>

    <section class="mb-10">
        <h2 class="text-2xl font-bold mb-4">Game Bookings</h2>
        @forelse ($gameBookings as $booking)
            <div class="bg-gray-800 p-4 rounded border border-gray-700 mb-4">
                <div class="flex flex-wrap justify-between gap-4">
                    <div>
                        <p class="text-white font-semibold">{{ $booking->game->name ?? 'Game' }}</p>
                        <p class="text-gray-400 text-sm">Time: {{ $booking->booking_time }}</p>
                        <p class="text-gray-400 text-sm">Players: {{ $booking->quantity }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-300 text-sm">Total: ${{ number_format($booking->total_price, 2) }}</p>
                        <p class="text-gray-400 text-sm">Status: {{ ucfirst($booking->status) }}</p>
                        <a href="{{ route('bookings.games.show', $booking) }}" class="text-sm text-red-300 hover:text-red-200">View details</a>
                        @if ($booking->status !== 'canceled')
                            <form method="POST" action="{{ route('bookings.games.cancel', $booking) }}" class="mt-2">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="bg-red-700 text-white px-3 py-1 rounded hover:bg-red-600">
                                    Cancel
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p class="text-gray-400">No game bookings yet.</p>
        @endforelse
        {{ $gameBookings->withQueryString()->links() }}
    </section>

    <section>
        <h2 class="text-2xl font-bold mb-4">Beach Events</h2>
        @forelse ($beachEventBookings as $booking)
            <div class="bg-gray-800 p-4 rounded border border-gray-700 mb-4">
                <div class="flex flex-wrap justify-between gap-4">
                    <div>
                        <p class="text-white font-semibold">{{ $booking->beachEvent->name ?? 'Beach Event' }}</p>
                        <p class="text-gray-400 text-sm">Date: {{ $booking->booking_date }}</p>
                        <p class="text-gray-400 text-sm">Time: {{ $booking->booking_time }}</p>
                        <p class="text-gray-400 text-sm">Tickets: {{ $booking->quantity }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-300 text-sm">Total: ${{ number_format($booking->total_price, 2) }}</p>
                        <p class="text-gray-400 text-sm">Status: {{ ucfirst($booking->status) }}</p>
                        <a href="{{ route('bookings.beach-events.show', $booking) }}" class="text-sm text-red-300 hover:text-red-200">View details</a>
                        @if ($booking->status !== 'canceled')
                            <form method="POST" action="{{ route('bookings.beach-events.cancel', $booking) }}" class="mt-2">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="bg-red-700 text-white px-3 py-1 rounded hover:bg-red-600">
                                    Cancel
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p class="text-gray-400">No beach event bookings yet.</p>
        @endforelse
        {{ $beachEventBookings->withQueryString()->links() }}
    </section>
</main>
@endsection
