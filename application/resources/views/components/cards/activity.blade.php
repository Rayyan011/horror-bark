@props([
    'item',
    'type',
    'bookingConfig' => [],
])

@php
    $isGame = $type === 'game';

    $route = $isGame
        ? route('bookings.games.store', $item)
        : route('bookings.rides.store', $item);

    $submitLabel = $bookingConfig['submitLabel']
        ?? ($isGame ? 'Book game' : 'Book ride');

    $buttonVariant = $isGame ? 'secondary' : 'primary';
    $quantityLabel = $isGame ? 'Players' : 'Tickets';
@endphp

<x-ui.entity-card
    :title="$item->name"
    :media="[
        'images' => $item->images ?? [],
        'fallback' => 'https://picsum.photos/seed/' . $item->id . '/400/300',
        'alt' => $item->name,
    ]"
    :meta="[
        ['label' => 'Island', 'value' => $item->island->name ?? 'Horror Island', 'tone' => 'muted'],
        ['label' => 'Price', 'value' => 'MVR ' . number_format($item->price, 2), 'tone' => 'muted'],
        ['label' => $isGame ? 'Max Players per Booking' : 'Max Capacity', 'value' => $isGame ? ($item->max_booking_quantity ?? 'N/A') : ($item->max_capacity ?? 'N/A'), 'tone' => 'muted'],
    ]"
    :description="$item->description ?? ($isGame ? 'Test your skills!' : 'Experience the thrill!')"
>
    <x-slot:footer>
        @auth
            <x-booking.form
                :action="$route"
                :mode="$bookingConfig['mode'] ?? 'datetime'"
                :rules-hint="$bookingConfig['rulesHint'] ?? 'Only 9:00 or 17:00.'"
                :submit-label="$submitLabel"
                :submit-variant="$buttonVariant"
                :quantity-config="[
                    'label' => $quantityLabel,
                    'min' => 1,
                    'max' => $item->max_booking_quantity,
                    'default' => 1,
                ]"
                :id-prefix="($isGame ? 'game_' : 'ride_') . $item->id"
            />
        @else
            <x-ui.auth-gate-cta :login-href="route('login')" label="Log in to book" />
        @endauth
    </x-slot:footer>
</x-ui.entity-card>
