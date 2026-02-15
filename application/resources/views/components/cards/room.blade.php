@props([
    'room',
    'openDetailsAction' => null,
])

<x-ui.surface>
    <h3 class="text-xl font-semibold text-white">{{ $room->room_number }}</h3>
    <p class="text-gray-400">Max Occupancy: {{ $room->max_occupancy }}</p>
    <p class="text-gray-400">Price: MVR {{ $room->price }}</p>
    @if (($room->available_spots ?? $room->max_occupancy) > 0)
        <p class="text-green-400 text-sm mt-1">{{ $room->available_spots ?? $room->max_occupancy }} / {{ $room->max_occupancy }} spots available</p>
    @else
        <p class="text-red-400 text-sm mt-1">Fully booked</p>
    @endif

    <button
        type="button"
        class="mt-4 bg-red-600 text-white py-2 px-4 rounded hover:bg-red-700"
        @if ($openDetailsAction)
            onclick="{{ $openDetailsAction }}"
        @else
            onclick="openUiModal('room-modal-{{ $room->id }}')"
        @endif
    >
        View Room Details
    </button>
</x-ui.surface>
