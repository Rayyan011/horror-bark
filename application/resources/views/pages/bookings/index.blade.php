@extends('layouts.app')

@section('title', 'My Bookings - Horror-Bark')

@section('content')
<main class="container mx-auto my-8 px-4">
    <h1 class="text-4xl font-bold mb-6 horror-font">My Bookings</h1>

    @if (session('status'))
        <div class="mb-4 text-green-300 text-sm">{{ session('status') }}</div>
    @endif

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
    </section>
</main>
@endsection
