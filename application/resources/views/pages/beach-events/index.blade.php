@extends('layouts.app')

@section('title', 'Beach Events - Horror-Bark')

@section('content')
    <!-- Main Content -->
<main class="container mx-auto my-8 px-4">
        <!-- Section Title -->
        <h1 class="text-4xl font-bold mb-6 horror-font text-center">Beach Events</h1>
        <p class="text-lg text-gray-300 mb-12 text-center">Eerie tides, moonlit concerts, and chilling fun.</p>

        @if (session('status'))
            <div class="mb-4 text-green-300 text-sm">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="mb-4 text-red-300 text-sm">{{ $errors->first() }}</div>
        @endif

        <form method="GET" class="bg-gray-800 p-4 rounded border border-gray-700 mb-8 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                <div class="lg:col-span-2">
                    <label class="block text-sm mb-1" for="search">Search</label>
                    <input id="search" name="search" type="text" value="{{ $filters['search'] ?? '' }}" placeholder="Event or organizer"
                        class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white" />
                </div>
                <div>
                    <label class="block text-sm mb-1" for="date_from">Date From</label>
                    <input id="date_from" name="date_from" type="date" value="{{ $filters['date_from'] ?? '' }}"
                        class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white" />
                </div>
                <div>
                    <label class="block text-sm mb-1" for="date_to">Date To</label>
                    <input id="date_to" name="date_to" type="date" value="{{ $filters['date_to'] ?? '' }}"
                        class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white" />
                </div>
                <div>
                    <label class="block text-sm mb-1" for="min_price">Min Price</label>
                    <input id="min_price" name="min_price" type="number" min="0" step="0.01" value="{{ $filters['min_price'] ?? '' }}"
                        class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white" />
                </div>
                <div>
                    <label class="block text-sm mb-1" for="max_price">Max Price</label>
                    <input id="max_price" name="max_price" type="number" min="0" step="0.01" value="{{ $filters['max_price'] ?? '' }}"
                        class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white" />
                </div>
                <div>
                    <label class="block text-sm mb-1" for="sort">Sort</label>
                    <select id="sort" name="sort" class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white">
                        <option value="date_asc" @selected(($filters['sort'] ?? 'date_asc') === 'date_asc')>Date (Soonest)</option>
                        <option value="date_desc" @selected(($filters['sort'] ?? '') === 'date_desc')>Date (Latest)</option>
                        <option value="price_asc" @selected(($filters['sort'] ?? '') === 'price_asc')>Price (Low-High)</option>
                        <option value="price_desc" @selected(($filters['sort'] ?? '') === 'price_desc')>Price (High-Low)</option>
                        <option value="name_asc" @selected(($filters['sort'] ?? '') === 'name_asc')>Name (A-Z)</option>
                        <option value="name_desc" @selected(($filters['sort'] ?? '') === 'name_desc')>Name (Z-A)</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Apply</button>
                <a href="{{ route('beach-events.index') }}" class="px-4 py-2 rounded border border-gray-600 text-gray-200 hover:text-white">Reset</a>
            </div>
        </form>

        <!-- Events Grid -->
        @if ($beachEvents->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($beachEvents as $event)
                    <x-beach-event-card :event="$event" />
                @endforeach
            </div>
            <div class="mt-6">
                {{ $beachEvents->links() }}
            </div>
        @else
            <p class="text-gray-300">No beach events match your filters.</p>
        @endif
    </main>
@endsection
