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
        @if ($beachEvents->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($beachEvents as $event)
                    <x-beach-event-card :event="$event" />
                @endforeach
            </div>
        @else
            <p class="text-gray-300">No beach events found. Please check back later!</p>
        @endif
    </main>
@endsection
