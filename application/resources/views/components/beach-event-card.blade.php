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
            <p class="text-gray-300 text-sm mb-2">
                <span class="font-semibold">Island:</span>
                {{ $event->island->name ?? 'Picnic Island' }}
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
            @auth
                <form method="POST" action="{{ route('bookings.beach-events.store', $event) }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-sm mb-1" for="booking_date_{{ $event->id }}">Booking date</label>
                        <input id="booking_date_{{ $event->id }}" name="booking_date" type="date" value="{{ $event->event_date }}" required
                            class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white" />
                    </div>
                    <div>
                        <label class="block text-sm mb-1" for="booking_time_{{ $event->id }}">Booking time</label>
                        <input id="booking_time_{{ $event->id }}" name="booking_time" type="time" required
                            class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white" />
                    </div>
                    <div>
                        <label class="block text-sm mb-1" for="quantity_{{ $event->id }}">Tickets</label>
                        <input id="quantity_{{ $event->id }}" name="quantity" type="number" min="1" max="{{ $event->max_booking_quantity }}" value="1" required
                            class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white" />
                    </div>
                    <button type="submit" class="w-full bg-red-600 text-white py-2 rounded hover:bg-red-700">
                        Book event
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="inline-block w-full text-center bg-red-600 text-white py-2 rounded hover:bg-red-700">
                    Log in to book
                </a>
            @endauth
        </div>
    </div>
</div>
