@props([
    'status',
    'items' => collect(),
    'openByDefault' => false,
    'count' => null,
])

@php
    $resolvedItems = $items instanceof \Illuminate\Support\Collection ? $items : collect($items);
    $resolvedCount = is_null($count) ? $resolvedItems->count() : $count;
@endphp

<details class="bg-gray-800 rounded border border-gray-700" @if($openByDefault) open @endif>
    <summary class="cursor-pointer list-none p-4 flex items-center justify-between">
        <span class="text-xl font-semibold">{{ $status }}</span>
        <span class="text-sm text-gray-400">{{ $resolvedCount }} booking(s)</span>
    </summary>

    <div class="px-4 pb-4 space-y-4">
        @forelse ($resolvedItems as $booking)
            <x-bookings.item :item="$booking" />
        @empty
            <p class="text-gray-400 text-sm">No {{ strtolower($status) }} bookings found for current filters.</p>
        @endforelse
    </div>
</details>
