@extends('layouts.app')

@section('title', 'Ferry Tickets - Horror-Bark Theme Park')

@section('content')
<main class="container mx-auto my-8 px-4">
    <h1 class="text-4xl font-bold mb-6 horror-font">Available Ferry Tickets</h1>

    @if($ferries->isEmpty())
        <p class="text-gray-300">No ferry tickets available at the moment.</p>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($ferries as $ferry)
                <div class="bg-gray-800 p-6 rounded shadow border border-gray-700">
                    <h2 class="text-2xl font-bold mb-2 text-white">{{ $ferry->name }}</h2>
                    {{-- Display other ferry details --}}
                    <p class="text-gray-400 mb-1">Price: ${{ number_format($ferry->price, 2) }}</p>
                    <p class="text-gray-400 mb-1">Max Capacity: {{ $ferry->max_capacity }}</p>
                    {{-- Add more details as needed from the Ferry model --}}

                    {{-- Add a "Book Now" or "View Details" button later --}}
                    <a href="#" class="inline-block mt-4 bg-red-600 text-white py-2 px-4 rounded hover:bg-red-700">
                        View Details (Coming Soon)
                    </a>
                </div>
            @endforeach
        </div>
    @endif
</main>
@endsection