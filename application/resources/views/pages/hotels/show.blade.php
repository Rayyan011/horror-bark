@extends('layouts.app')

@section('title', $hotel->name . ' - Horror-Bark Theme Park')

@section('content')
<main class="container mx-auto my-8 px-4">
    <h1 class="text-4xl font-bold mb-6">{{ $hotel->name }}</h1>
    @if (!empty($hotel->images) && is_array($hotel->images))
        <img src="{{ asset($hotel->images[0]) }}" alt="{{ $hotel->name }}" class="rounded mb-8
             w-full h-64 object-cover">
    @endif
    <p class="text-gray-300 mb-8">{{ $hotel->location }}</p>

    <h2 class="text-2xl font-bold mb-4 horror-font">Available Rooms</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach ($hotel->rooms as $room)
            <div class="bg-gray-800 p-6 rounded shadow border border-gray-700">
                <h3 class="text-xl font-semibold text-white">{{ $room->room_number }}</h3>
                <p class="text-gray-400">Max Occupancy: {{ $room->max_occupancy }}</p>
                <p class="text-gray-400">Price: ${{ $room->price }}</p>
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
                    <p class="text-gray-400 mb-2">Price: ${{ $room->price }}</p>
                    <p class="text-gray-400 mb-2">Status: {{ ucfirst($room->status) }}</p>
                    @if (!empty($room->amenities))
                        <h5 class="text-white font-semibold mt-4 mb-2">Amenities:</h5>
                        <ul class="list-disc list-inside text-gray-400">
                            @foreach ($room->amenities as $amenity)
                                <li>{{ $amenity }}</li>
                            @endforeach
                        </ul>
                    @endif
                    @if (!empty($room->images) && is_array($room->images))
                        <img src="{{ asset($room->images[0]) }}" alt="Room Image" class="rounded
                             mt-4 w-full h-48 object-cover">
                    @else
                        <img src="https://picsum.photos/400/300?random={{ $room->id }}" alt="Room
                             Image" class="rounded mt-4 w-full h-48 object-cover">
                    @endif
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