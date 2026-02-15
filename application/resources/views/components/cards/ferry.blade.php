@props([
    'ferry',
    'bookingConfig' => [],
])

<x-ui.entity-card
    :title="$ferry->name"
    :meta="[
        ['label' => 'Island', 'value' => $ferry->island->name ?? 'Horror Island', 'tone' => 'muted'],
        ['label' => 'Price', 'value' => 'MVR ' . number_format($ferry->price, 2), 'tone' => 'muted'],
        ['label' => 'Max Capacity', 'value' => $ferry->max_capacity, 'tone' => 'muted'],
    ]"
>
    <x-slot:footer>
        @auth
            <x-booking.form
                :action="route('bookings.ferries.store', $ferry)"
                :mode="$bookingConfig['mode'] ?? 'datetime'"
                :rules-hint="$bookingConfig['rulesHint'] ?? 'Whole hour between 9:00 and 16:00.'"
                :submit-label="$bookingConfig['submitLabel'] ?? 'Book ferry'"
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
