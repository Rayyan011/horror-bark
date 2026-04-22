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

<details class="theme-status-shell" @if($openByDefault) open @endif>
    <summary class="theme-status-summary cursor-pointer list-none">
        <span class="text-xl font-semibold text-moonlight">{{ $status }}</span>
        <span class="theme-kicker">{{ $resolvedCount }} booking(s)</span>
    </summary>

    <div class="px-4 pb-4 space-y-4">
        @forelse ($resolvedItems as $booking)
            <x-bookings.item :item="$booking" />
        @empty
            <p class="readable-muted">No {{ strtolower($status) }} bookings found for current filters.</p>
        @endforelse
    </div>
</details>
