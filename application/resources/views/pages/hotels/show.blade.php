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

            <x-ui.modal :id="'room-modal-' . $room->id" :title="$room->room_number" size="wide">
                <x-slot:body>
                    <div class="grid gap-4 xl:grid-cols-[minmax(14rem,0.72fr)_minmax(18rem,0.9fr)_minmax(22rem,1fr)]">
                        <section class="space-y-3">
                            <div class="grid grid-cols-2 gap-3 text-sm xl:grid-cols-1">
                                <div class="border border-primary-light/10 bg-black/25 p-3">
                                    <p class="theme-label">Occupancy</p>
                                    <p class="mt-1 text-moonlight">{{ $room->max_occupancy }}</p>
                                </div>
                                <div class="border border-primary-light/10 bg-black/25 p-3">
                                    <p class="theme-label">Nightly</p>
                                    <p class="mt-1 text-moonlight">MVR {{ $room->price }}</p>
                                </div>
                                <div class="border border-primary-light/10 bg-black/25 p-3">
                                    <p class="theme-label">Status</p>
                                    <p class="mt-1 text-moonlight">{{ ucfirst($room->status) }}</p>
                                </div>
                                <div class="border border-primary-light/10 bg-black/25 p-3">
                                    <p class="theme-label">Available</p>
                                    @if (($room->available_spots ?? $room->max_occupancy) > 0)
                                        <p class="mt-1 text-green-400">{{ $room->available_spots ?? $room->max_occupancy }} / {{ $room->max_occupancy }}</p>
                                    @else
                                        <p class="mt-1 text-red-400">Fully booked</p>
                                    @endif
                                </div>
                            </div>
                        </section>

                        <section class="space-y-3">
                            <div class="overflow-hidden border border-primary-light/10">
                                <x-ui.media-gallery
                                    :images="$room->images ?? []"
                                    :fallback-src="\App\Support\HorrorGeneratedMediaCatalog::path('fallbacks', 'room')"
                                    :alt="$room->room_number"
                                    height="h-32 sm:h-40 xl:h-48"
                                />
                            </div>

                            @if (!empty($room->amenities))
                                <div class="space-y-2">
                                    <h5 class="text-sm font-semibold uppercase tracking-[0.18em] text-white">Amenities</h5>
                                    <ul class="grid gap-1 text-sm text-primary-light/70">
                                        @foreach ($room->amenities as $amenity)
                                            <li class="flex gap-2">
                                                <span class="text-primary-light/40">&bull;</span>
                                                <span>{{ $amenity }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </section>

                        <section class="border border-primary-light/10 bg-black/25 p-4">
                            @if (($room->available_spots ?? $room->max_occupancy) > 0)
                                @auth
                                    <x-booking.form
                                        :action="route('checkout.hotels.prepare', $room)"
                                        mode="date-range"
                                        submit-label="Review & pay"
                                        rules-hint="You will confirm this room booking during payment review."
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
                        </section>
                    </div>
                </x-slot:body>
            </x-ui.modal>
        @endforeach
    </div>
</main>
@endsection
