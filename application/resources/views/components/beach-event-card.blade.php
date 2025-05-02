@props(['event'])

<div class="bg-gray-800 shadow-lg rounded overflow-hidden border border-gray-700 flex flex-col">
    @if (!empty($event->images))
        <x-image-carousel :images="$event->images" :title="$event->name" />
    @else
        <img src="https://picsum.photos/seed/{{ $event->id }}/400/300" alt="{{ $event->name }} image" class="w-full h-48 object-cover" />
    @endif

    <div class="p-4 flex flex-col justify-between flex-1">
        <div>
            <h4 class="font-bold text-xl mb-2 horror-font">{{ $event->name }}</h4>
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

        <div class="mt-4">
            @livewire('book-now')
        </div>
    </div>
</div>
