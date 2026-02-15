@extends('layouts.app')

@section('title', 'Hotels - Horror-Bark Theme Park')

@section('content')
<main class="container mx-auto my-8 px-4">
    <h1 class="text-4xl font-bold mb-6">Our Hotels</h1>

    <form method="GET" class="bg-gray-800 p-4 rounded border border-gray-700 mb-6 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="lg:col-span-2">
                <label class="block text-sm mb-1" for="search">Search</label>
                <input
                    id="search"
                    name="search"
                    type="text"
                    value="{{ $filters['search'] ?? '' }}"
                    placeholder="Hotel or location"
                    class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white"
                />
            </div>
            <div>
                <label class="block text-sm mb-1" for="min_price">Min Price</label>
                <input
                    id="min_price"
                    name="min_price"
                    type="number"
                    min="0"
                    step="0.01"
                    value="{{ $filters['min_price'] ?? '' }}"
                    class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white"
                />
            </div>
            <div>
                <label class="block text-sm mb-1" for="max_price">Max Price</label>
                <input
                    id="max_price"
                    name="max_price"
                    type="number"
                    min="0"
                    step="0.01"
                    value="{{ $filters['max_price'] ?? '' }}"
                    class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white"
                />
            </div>
            <div>
                <label class="block text-sm mb-1" for="min_occupancy">Min Occupancy</label>
                <input
                    id="min_occupancy"
                    name="min_occupancy"
                    type="number"
                    min="1"
                    value="{{ $filters['min_occupancy'] ?? '' }}"
                    class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white"
                />
            </div>
            <div>
                <label class="block text-sm mb-1" for="sort">Sort</label>
                <select id="sort" name="sort" class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white">
                    <option value="name_asc" @selected(($filters['sort'] ?? 'name_asc') === 'name_asc')>Name (A-Z)</option>
                    <option value="name_desc" @selected(($filters['sort'] ?? '') === 'name_desc')>Name (Z-A)</option>
                    <option value="price_asc" @selected(($filters['sort'] ?? '') === 'price_asc')>Price (Low-High)</option>
                    <option value="price_desc" @selected(($filters['sort'] ?? '') === 'price_desc')>Price (High-Low)</option>
                </select>
            </div>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Apply</button>
            <a href="{{ route('hotels.index') }}" class="px-4 py-2 rounded border border-gray-600 text-gray-200 hover:text-white">Reset</a>
        </div>
    </form>

    @if($hotels->isEmpty())
        <p class="text-gray-300">No hotels match your filters.</p>
    @else
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach ($hotels as $hotel)
                @php
                    $cheapestRoom = $hotel->rooms->sortBy('price')->first();
                @endphp
                <div class="bg-gray-800 p-6 rounded shadow border border-gray-700">
                    <h2 class="text-2xl font-bold mb-2 text-white">{{ $hotel->name }}</h2>
                    <p class="text-gray-400">{{ $hotel->location }}</p>
                    <p class="text-gray-400 text-sm mb-3">Island: Horror Island</p>
                    @if ($cheapestRoom)
                        <p class="text-gray-300 text-sm mb-3">From ${{ number_format($cheapestRoom->price, 2) }} / night</p>
                    @endif
                    @if (!empty($hotel->images))
                        <x-image-carousel :images="$hotel->images" :title="$hotel->name" />
                    @else
                        <img src="https://picsum.photos/seed/{{ $hotel->id }}/400/300" alt="{{ $hotel->name }} image" class="w-full h-48 object-cover" />
                    @endif
                    <a href="{{ route('hotels.show', $hotel) }}" class="inline-block mt-4 bg-red-600 text-white py-2 px-4 rounded hover:bg-red-700">
                        View Hotel
                    </a>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $hotels->links() }}
        </div>
    @endif
</main>
@endsection
