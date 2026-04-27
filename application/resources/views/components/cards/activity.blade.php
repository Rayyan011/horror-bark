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
        ['label' => 'District', 'value' => $item->location ?? $item->island->name ?? 'Horror Island', 'tone' => 'muted'],
        ['label' => 'Price', 'value' => 'MVR ' . number_format($item->price, 2), 'tone' => 'muted'],
        ['label' => 'Max Capacity', 'value' => $item->max_capacity ?? 'N/A', 'tone' => 'muted'],
        ['label' => 'Max Booking Qty', 'value' => $item->max_booking_quantity ?? 'N/A', 'tone' => 'muted'],
    ]"
    :description="$item->description ?? ($isGame ? 'Test your skills!' : 'Experience the thrill!')"
>
    <x-slot:footer>
        @auth
            @if (($bookingConfig['mode'] ?? null) === 'hotel-gated-slot')
                <x-booking.time-slot-form
                    :action="$route"
                    :rules-hint="$bookingConfig['rulesHint'] ?? null"
                    :submit-label="$submitLabel"
                    :submit-variant="$buttonVariant"
                    :quantity-config="[
                        'label' => $quantityLabel,
                        'min' => 1,
                        'max' => $item->max_booking_quantity,
                        'default' => 1,
                    ]"
                    :time-options="$bookingConfig['timeOptions'] ?? ['09:00', '17:00']"
                    :date-options="$bookingConfig['dateOptions'] ?? []"
                    :date-min="$bookingConfig['dateMin'] ?? null"
                    :date-max="$bookingConfig['dateMax'] ?? null"
                    :requires-hotel="true"
                    :hotel-stay-windows="$bookingConfig['hotelStayWindows'] ?? []"
                    :disabled="$bookingConfig['disabled'] ?? false"
                    :disabled-reason="$bookingConfig['disabledReason'] ?? null"
                    :invalid-date-message="$bookingConfig['invalidDateMessage'] ?? 'Choose a date during your confirmed hotel stay.'"
                    :future-message="$bookingConfig['futureMessage'] ?? ($isGame ? 'Choose a future game time.' : 'Choose a future ride time.')"
                    :id-prefix="($isGame ? 'game_' : 'ride_') . $item->id"
                />
            @else
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
            @endif
        @else
            <x-ui.auth-gate-cta :login-href="route('login')" label="Log in to book" />
        @endauth
    </x-slot:footer>
</x-ui.entity-card>
