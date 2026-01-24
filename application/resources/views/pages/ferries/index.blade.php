@extends('layouts.app')

@section('title', 'Ferry Tickets - Horror-Bark Theme Park')

@section('content')
<main class="container mx-auto my-8 px-4">
    <h1 class="text-4xl font-bold mb-6 horror-font">Available Ferry Tickets</h1>

    @if (session('status'))
        <div class="mb-4 text-green-300 text-sm">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 text-red-300 text-sm">{{ $errors->first() }}</div>
    @endif

    @if($ferries->isEmpty())
        <p class="text-gray-300">No ferry tickets available at the moment.</p>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($ferries as $ferry)
                <div class="bg-gray-800 p-6 rounded shadow border border-gray-700">
                    <h2 class="text-2xl font-bold mb-2 text-white">{{ $ferry->name }}</h2>
                    {{-- Display other ferry details --}}
                    <p class="text-gray-400 mb-1">Price: ${{ number_format($ferry->price, 2) }}</p>
                    <p class="text-gray-400 mb-1">Max Capacity: {{ $ferry->max_capacity }}</p>
                    {{-- Add more details as needed from the Ferry model --}}

                    <div class="mt-4">
                        @auth
                            <form method="POST" action="{{ route('bookings.ferries.store', $ferry) }}" class="space-y-3">
                                @csrf
                                <div>
                                    <label class="block text-sm mb-1" for="booking_time_{{ $ferry->id }}">Booking time</label>
                                    <input id="booking_time_{{ $ferry->id }}" name="booking_time" type="datetime-local" required
                                        class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white" />
                                    <p class="text-xs text-gray-400 mt-1">Whole hour between 9:00 and 16:00.</p>
                                </div>
                                <div>
                                    <label class="block text-sm mb-1" for="quantity_{{ $ferry->id }}">Tickets</label>
                                    <input id="quantity_{{ $ferry->id }}" name="quantity" type="number" min="1" max="{{ $ferry->max_booking_quantity }}" value="1" required
                                        class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white" />
                                </div>
                                <button type="submit" class="w-full bg-red-600 text-white py-2 rounded hover:bg-red-700">
                                    Book ferry
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
</main>
@endsection