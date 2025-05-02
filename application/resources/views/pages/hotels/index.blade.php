@extends('layouts.app')

@section('title', 'Hotels - Horror-Bark Theme Park')

@section('content')
<main class="container mx-auto my-8 px-4">
    <h1 class="text-4xl font-bold mb-6">Our Hotels</h1>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach ($hotels as $hotel)
            <div class="bg-gray-800 p-6 rounded shadow border border-gray-700">
                <h2 class="text-2xl font-bold mb-2 text-white">{{ $hotel->name }}</h2>
                <p class="text-gray-400 mb-4">{{ $hotel->location }}</p>
                @if (!empty($hotel->images))
                    <x-image-carousel :images="$hotel->images" :title="$hotel->name" />
                @else
                    <img src="https://picsum.photos/seed/{{ $room->id }}/400/300" alt="{{ $room->name }} image" class="w-full h-48 object-cover" />
                @endif
                <a href="{{ route('hotels.show', $hotel) }}" class="inline-block mt-4 bg-red-600
                        text-white py-2 px-4 rounded hover:bg-red-700">
                    View Hotel
                </a>
            </div>
        @endforeach
    </div>
</main>
@endsection