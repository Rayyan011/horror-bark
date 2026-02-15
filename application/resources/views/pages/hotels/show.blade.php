@extends('layouts.app')

@section('title', $hotel->name . ' - Horror-Bark Theme Park')

@section('content')
<main class="container mx-auto my-8 px-4">
    <h1 class="text-4xl font-bold mb-6">{{ $hotel->name }}</h1>
    {{-- @if (!empty($hotel->images) && is_array($hotel->images))
        <img src="{{ asset($hotel->images[0]) }}" alt="{{ $hotel->name }}" class="rounded mb-8
             w-full h-64 object-cover">
    @endif --}}
    @if (session('status'))
        <div class="mb-4 text-green-300 text-sm">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 text-red-300 text-sm">{{ $errors->first() }}</div>
    @endif

    @if (!empty($hotel->images))
        <x-image-carousel :images="$hotel->images" :title="$hotel->name" />
    @else
        <img src="https://picsum.photos/seed/{{ $hotel->id }}/400/300" alt="{{ $hotel->name }} image" class="w-full h-48 object-cover" />
    @endif
    <p class="text-gray-300 mb-8">{{ $hotel->location }}</p>

    <h2 class="text-2xl font-bold mb-4 horror-font">Available Rooms</h2>
    @if ($hotel->rooms->isEmpty())
        <p class="text-gray-400">No rooms are currently available at this hotel.</p>
    @endif
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach ($hotel->rooms as $room)
            <div class="bg-gray-800 p-6 rounded shadow border border-gray-700">
                <h3 class="text-xl font-semibold text-white">{{ $room->room_number }}</h3>
                <p class="text-gray-400">Max Occupancy: {{ $room->max_occupancy }}</p>
                <p class="text-gray-400">Price: MVR {{ $room->price }}</p>
                @if ($room->available_spots > 0)
                    <p class="text-green-400 text-sm mt-1">{{ $room->available_spots }} / {{ $room->max_occupancy }} spots available</p>
                @else
                    <p class="text-red-400 text-sm mt-1">Fully booked</p>
                @endif
                <button
                    class="mt-4 bg-red-600 text-white py-2 px-4 rounded hover:bg-red-700"
                    onclick="openModal({{ $room->id }})"
                >
                    View Room Details
                </button>
            </div>
            <div id="modal-{{ $room->id }}" class="hidden fixed inset-0 bg-black bg-opacity-70
                 flex items-center justify-center z-50">
                <div class="bg-gray-900 p-6 rounded-lg w-96 relative">
                    <button
                        onclick="closeModal({{ $room->id }})"
                        class="absolute top-2 right-2 text-white text-xl">&times;
                    </button>
                    <h4 class="text-2xl font-bold mb-4 text-white">{{ $room->room_number }}</h4>
                    <p class="text-gray-400 mb-2">Max Occupancy: {{ $room->max_occupancy }}</p>
                    <p class="text-gray-400 mb-2">Price: MVR {{ $room->price }}</p>
                    <p class="text-gray-400 mb-2">Status: {{ ucfirst($room->status) }}</p>
                    @if (!empty($room->amenities))
                        <h5 class="text-white font-semibold mt-4 mb-2">Amenities:</h5>
                        <ul class="list-disc list-inside text-gray-400">
                            @foreach ($room->amenities as $amenity)
                                <li>{{ $amenity }}</li>
                            @endforeach
                        </ul>
                    @endif
                    @if (!empty($room->images))
                        <x-image-carousel :images="$room->images" :title="$room->name" />
                    @else
                        <img src="https://picsum.photos/seed/{{ $room->id }}/400/300" alt="{{ $room->room_number }} image" class="w-full h-48 object-cover" />
                    @endif
                    <div class="mt-4 border-t border-gray-700 pt-4">
                        @if ($room->available_spots > 0)
                            @auth
                                <p class="text-green-400 text-sm mb-3">{{ $room->available_spots }} / {{ $room->max_occupancy }} spots available</p>
                                <form method="POST" action="{{ route('bookings.hotels.store', $room) }}" class="space-y-3">
                                    @csrf
                                    <div>
                                        <label class="block text-sm mb-1" for="start_date_{{ $room->id }}">Check-in</label>
                                        <input id="start_date_{{ $room->id }}" name="start_date" type="date" required
                                            class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white" />
                                    </div>
                                    <div>
                                        <label class="block text-sm mb-1" for="end_date_{{ $room->id }}">Check-out</label>
                                        <input id="end_date_{{ $room->id }}" name="end_date" type="date" required
                                            class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white" />
                                    </div>
                                    <div>
                                        <label class="block text-sm mb-1" for="quantity_{{ $room->id }}">Guests</label>
                                        <input id="quantity_{{ $room->id }}" name="quantity" type="number" min="1" max="{{ $room->available_spots }}" value="1" required
                                            class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white" />
                                    </div>
                                    <button type="submit" class="w-full bg-red-600 text-white py-2 rounded hover:bg-red-700">
                                        Book this room
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="inline-block w-full text-center bg-red-600 text-white py-2 rounded hover:bg-red-700">
                                    Log in to book
                                </a>
                            @endauth
                        @else
                            <p class="text-red-400 text-sm">This room is fully booked. Please check back later.</p>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</main>
<script>
    function openModal(id) {
        document.getElementById('modal-' + id).classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById('modal-' + id).classList.add('hidden');
    }
</script>
@endsection