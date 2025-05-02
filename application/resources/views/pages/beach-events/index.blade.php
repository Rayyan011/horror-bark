@extends('layouts.app')

@section('title', 'Beach Events - Horror-Bark')

@section('content')
    <!-- Main Content -->
    <main class="container mx-auto my-8 px-4">
        <!-- Section Title -->
        <h1 class="text-4xl font-bold mb-6 horror-font text-center">Beach Events</h1>
        <p class="text-lg text-gray-300 mb-12 text-center">Eerie tides, moonlit concerts, and chilling fun.</p>

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
