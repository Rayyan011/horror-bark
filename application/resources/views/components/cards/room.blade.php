@props([
    'room',
    'openDetailsAction' => null,
])

@php
    $detailsAction = $openDetailsAction ?: "openUiModal('room-modal-{$room->id}')";
@endphp

<x-ui.surface>
    <div class="mb-4 overflow-hidden rounded-2xl border border-primary-light/10">
        <x-ui.media-gallery
            :images="$room->images ?? []"
            :fallback-src="\App\Support\HorrorGeneratedMediaCatalog::path('fallbacks', 'room')"
            :alt="$room->room_number"
            height="h-48"
        />
    </div>

    <h3 class="text-xl font-semibold text-white">{{ $room->room_number }}</h3>
    @if (filled($room->description ?? null))
        <p class="mt-2 text-sm leading-relaxed text-primary-light/80">
            {{ \Illuminate\Support\Str::limit($room->description, 132) }}
        </p>
    @endif

    <div class="mt-4 space-y-1">
    <p class="readable-muted">Max Occupancy: {{ $room->max_occupancy }}</p>
    <p class="readable-muted">Price: MVR {{ $room->price }}</p>
    @if (($room->available_spots ?? $room->max_occupancy) > 0)
        <p class="text-green-400 text-sm mt-1">{{ $room->available_spots ?? $room->max_occupancy }} / {{ $room->max_occupancy }} spots available</p>
    @else
        <p class="text-red-400 text-sm mt-1">Fully booked</p>
    @endif
    </div>

    <x-ui.button
        type="button"
        variant="secondary"
        block
        class="mt-4"
        onclick="{{ $detailsAction }}"
    >
        View Room Details
    </x-ui.button>
</x-ui.surface>
