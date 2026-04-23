@props([
    'ferry',
    'bookingConfig' => [],
])

<x-ui.entity-card
    :title="$ferry->name"
    :media="[
        'images' => $ferry->images ?? [],
        'fallback' => \App\Support\HorrorGeneratedMediaCatalog::path('fallbacks', 'ferry'),
        'alt' => $ferry->name,
    ]"
    :meta="[
        ['label' => 'Destination', 'value' => $ferry->island->name ?? 'Horror Island', 'tone' => 'muted'],
        ['label' => 'Price', 'value' => 'MVR ' . number_format($ferry->price, 2), 'tone' => 'muted'],
        ['label' => 'Max Capacity', 'value' => $ferry->max_capacity, 'tone' => 'muted'],
    ]"
    :description="$ferry->description"
>
    <x-slot:footer>
        @auth
            <x-booking.form
                :action="route('checkout.ferries.prepare', $ferry)"
                :mode="$bookingConfig['mode'] ?? 'datetime'"
                :rules-hint="$bookingConfig['rulesHint'] ?? 'Whole hour between 9:00 and 16:00. Payment is confirmed on the next screen.'"
                :submit-label="$bookingConfig['submitLabel'] ?? 'Review & pay'"
                :quantity-config="[
                    'label' => 'Tickets',
                    'min' => 1,
                    'max' => $ferry->max_booking_quantity,
                    'default' => 1,
                ]"
                :id-prefix="'ferry_' . $ferry->id"
            />
        @else
            <x-ui.auth-gate-cta :login-href="route('login')" label="Log in to book" />
        @endauth
    </x-slot:footer>
</x-ui.entity-card>
