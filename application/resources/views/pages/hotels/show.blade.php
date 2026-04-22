@extends('layouts.app')

@section('title', $hotel->name . ' - Horror-Bark Theme Park')

@section('content')
<main class="space-y-6">
    <x-ui.section-heading :title="$hotel->name" size="xl" />

    <x-ui.alert-stack />

    <x-ui.media-gallery
        :images="$hotel->images ?? []"
        :fallback-src="\App\Support\HorrorGeneratedMediaCatalog::path('fallbacks', 'hotel')"
        :alt="$hotel->name"
    />

    <p class="readable-copy">{{ $hotel->location }}</p>
    @if (filled($hotel->description))
        <p class="readable-copy">{{ $hotel->description }}</p>
    @endif

    <x-ui.section-heading title="Available Rooms" size="md" />

    @if ($hotel->rooms->isEmpty())
        <p class="readable-muted">No rooms are currently available at this hotel.</p>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach ($hotel->rooms as $room)
            <x-cards.room :room="$room" />

            <x-ui.modal :id="'room-modal-' . $room->id" :title="$room->room_number" size="md">
                <x-slot:body>
                    <p class="readable-muted mb-2">Max Occupancy: {{ $room->max_occupancy }}</p>
                    <p class="readable-muted mb-2">Price: MVR {{ $room->price }}</p>
                    <p class="readable-muted mb-2">Status: {{ ucfirst($room->status) }}</p>
                    @if (($room->available_spots ?? $room->max_occupancy) > 0)
                        <p class="text-green-400 text-sm mb-2">{{ $room->available_spots ?? $room->max_occupancy }} / {{ $room->max_occupancy }} spots available</p>
                    @else
                        <p class="text-red-400 text-sm mb-2">Fully booked</p>
                    @endif

                    @if (!empty($room->amenities))
                        <h5 class="text-white font-semibold mt-4 mb-2">Amenities:</h5>
                        <ul class="list-disc list-inside readable-muted">
                            @foreach ($room->amenities as $amenity)
                                <li>{{ $amenity }}</li>
                            @endforeach
                        </ul>
                    @endif

                    <div class="mt-4">
                        <x-ui.media-gallery
                            :images="$room->images ?? []"
                            :fallback-src="\App\Support\HorrorGeneratedMediaCatalog::path('fallbacks', 'room')"
                            :alt="$room->room_number"
                        />
                    </div>
                </x-slot:body>

                <x-slot:footer>
                    @if (($room->available_spots ?? $room->max_occupancy) > 0)
                        @auth
                            <x-booking.form
                                :action="route('checkout.hotels.prepare', $room)"
                                mode="date-range"
                                submit-label="Review & pay"
                                rules-hint="You will confirm this room booking on the demo payment screen."
                                :quantity-config="[
                                    'label' => 'Guests',
                                    'min' => 1,
                                    'max' => $room->available_spots ?? $room->max_occupancy,
                                    'default' => 1,
                                ]"
                                :id-prefix="'room_' . $room->id"
                            />
                        @else
                            <x-ui.auth-gate-cta :login-href="route('login')" label="Log in to book" />
                        @endauth
                    @else
                        <p class="text-red-400 text-sm">This room is fully booked. Please check back later.</p>
                    @endif
                </x-slot:footer>
            </x-ui.modal>
        @endforeach
    </div>
</main>
@endsection
