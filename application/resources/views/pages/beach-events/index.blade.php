@extends('layouts.app')

@section('title', 'Beach Events - Horror-Bark')

@section('content')
<!-- Hero Section -->
<section class="bg-cover bg-center h-96"
    style="background-image: url('https://picsum.photos/seed/beach-horror/1200/600');">
    <div class="bg-black bg-opacity-70 h-full flex items-center justify-center">
        <div class="text-center">
            <h2 class="text-5xl font-bold mb-4 horror-font">Beach Events</h2>
            <p class="text-xl">Eerie tides, moonlit concerts, and chilling fun.</p>
        </div>
    </div>
</section>

<!-- Main Content -->
<main class="container mx-auto my-8 px-4">
    <!-- Section Title -->
    <h3 class="text-2xl font-bold mb-6 horror-font">Explore Our Upcoming Beach Events</h3>

    <!-- Events Grid -->
    @if($beachEvents->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($beachEvents as $event)
        <!-- Single Event Card -->
        <div class="bg-gray-800 shadow-lg rounded overflow-hidden border border-gray-700 flex flex-col">
            @if($event->cover_image)
                <img src="{{ asset('storage/' . $event->cover_image) }}"
                    alt="{{ $event->name }} image"
                    class="w-full h-48 object-cover" />
            @else
                <img src="https://picsum.photos/seed/{{ $event->id }}/400/300"
                    alt="{{ $event->name }} image"
                    class="w-full h-48 object-cover" />
            @endif

            <!-- Card Content -->
            <div class="p-4 flex flex-col justify-between flex-1">
                <div>
                    <h4 class="font-bold text-xl mb-2 horror-font">
                        {{ $event->name }}
                    </h4>
                    <p class="text-gray-300 text-sm mb-2">
                        <span class="font-semibold">Organizer:</span>
                        {{ $event->owner->name ?? 'N/A' }}
                    </p>
                    <p class="text-gray-300 text-sm">
                        <span class="font-semibold">Date:</span>
                        {{ optional($event->event_date)->format('F d, Y') }}
                    </p>
                    <p class="text-gray-300 text-sm">
                        <span class="font-semibold">Price:</span> ${{ number_format($event->price, 2) }}
                    </p>
                    <p class="text-gray-300 text-sm">
                        <span class="font-semibold">Max Capacity:</span> {{ $event->max_capacity }}
                    </p>
                    <p class="text-gray-300 text-sm">
                        <span class="font-semibold">Max Booking Qty:</span> {{ $event->max_booking_quantity }}
                    </p>
                </div>

                <!-- Action Button -->
                <div class="mt-4">
                    <!-- Adjust the link to point to a booking or detail page -->
                    <a
                        href="#"
                        class="inline-block bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
                        Book Now
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <p class="text-gray-300">No beach events found. Please check back later!</p>
    @endif
</main>
@endsection