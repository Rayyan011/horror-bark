@props([
    'item',
    'type',
    'bookingConfig' => [],
])

@php
    $isGame = $type === 'game';

    $route = $isGame
        ? route('checkout.games.prepare', $item)
        : route('checkout.rides.prepare', $item);

    $submitLabel = $bookingConfig['submitLabel']
        ?? 'Review & pay';

    $buttonVariant = $isGame ? 'secondary' : 'primary';
    $quantityLabel = $isGame ? 'Players' : 'Tickets';
@endphp

<x-ui.entity-card
    :title="$item->name"
    :media="[
        'images' => $item->images ?? [],
        'fallback' => \App\Support\HorrorGeneratedMediaCatalog::path('fallbacks', $isGame ? 'game' : 'ride'),
        'alt' => $item->name,
    ]"
    :meta="[
        ['label' => 'Type', 'value' => $isGame ? 'Game' : 'Ride'],
        ['label' => 'Island', 'value' => $item->island->name ?? 'Horror Island', 'tone' => 'muted'],
        ['label' => 'Price', 'value' => 'MVR ' . number_format($item->price, 2), 'tone' => 'muted'],
        ['label' => 'Max Capacity', 'value' => $item->max_capacity ?? 'N/A', 'tone' => 'muted'],
        ['label' => 'Max Booking Qty', 'value' => $item->max_booking_quantity ?? 'N/A', 'tone' => 'muted'],
    ]"
    :description="$item->description ?? ($isGame ? 'Test your skills!' : 'Experience the thrill!')"
>
    <x-slot:footer>
        @auth
            <x-booking.form
                :action="$route"
                :mode="$bookingConfig['mode'] ?? 'datetime'"
                :rules-hint="$bookingConfig['rulesHint'] ?? 'Only 9:00 or 17:00. Payment is confirmed on the next screen.'"
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
