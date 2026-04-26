@props([
    'event',
    'bookingConfig' => [],
])

<x-ui.entity-card
    :title="$event->name"
    :media="[
        'images' => $event->images ?? [],
        'fallback' => \App\Support\HorrorGeneratedMediaCatalog::path('fallbacks', 'beach-event'),
        'alt' => $event->name,
    ]"
    :meta="[
        ['label' => 'Organizer', 'value' => $event->owner->name ?? 'N/A', 'tone' => 'muted'],
        ['label' => 'Shore', 'value' => $event->island->name ?? 'Picnic Island', 'tone' => 'muted'],
        ['label' => 'Date', 'value' => optional($event->event_date)->format('F d, Y'), 'tone' => 'muted'],
        ['label' => 'Price', 'value' => 'MVR ' . number_format($event->price, 2), 'tone' => 'muted'],
        ['label' => 'Max Capacity', 'value' => $event->max_capacity, 'tone' => 'muted'],
        ['label' => 'Max Booking Qty', 'value' => $event->max_booking_quantity, 'tone' => 'muted'],
    ]"
    :description="$event->description"
>
    <x-slot:footer>
        @auth
            <x-booking.form
                :action="route('checkout.beach-events.prepare', $event)"
                mode="date-time"
                :submit-label="$bookingConfig['submitLabel'] ?? 'Review & pay'"
                rules-hint="Confirmed hotel stay required before payment review."
                :quantity-config="[
                    'label' => 'Tickets',
                    'min' => 1,
                    'max' => $event->max_booking_quantity,
                    'default' => 1,
                ]"
                :values="[
                    'date_value' => $event->event_date,
                ]"
                :id-prefix="'beach_event_' . $event->id"
            />
        @else
            <x-ui.auth-gate-cta :login-href="route('login')" label="Log in to book" />
        @endauth
    </x-slot:footer>
</x-ui.entity-card>
