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
        ['label' => 'Destination', 'value' => $ferry->location ?? $ferry->island->name ?? 'Horror Island', 'tone' => 'muted'],
        ['label' => 'Price', 'value' => 'MVR ' . number_format($ferry->price, 2), 'tone' => 'muted'],
        ['label' => 'Max Capacity', 'value' => $ferry->max_capacity, 'tone' => 'muted'],
    ]"
    :description="$ferry->description"
>
    <x-slot:footer>
        @auth
            @if (($bookingConfig['mode'] ?? null) === 'ferry-window')
                <x-booking.time-slot-form
                    :action="route('checkout.ferries.prepare', $ferry)"
                    :rules-hint="$bookingConfig['rulesHint'] ?? null"
                    :submit-label="$bookingConfig['submitLabel'] ?? 'Book ferry'"
                    :quantity-config="[
                        'label' => 'Tickets',
                        'min' => 1,
                        'max' => $ferry->max_booking_quantity,
                        'default' => 1,
                    ]"
                    :time-options="$bookingConfig['timeOptions'] ?? null"
                    :date-options="$bookingConfig['dateOptions'] ?? []"
                    :date-min="$bookingConfig['dateMin'] ?? null"
                    :date-max="$bookingConfig['dateMax'] ?? null"
                    :requires-hotel="$bookingConfig['requiresHotel'] ?? false"
                    :hotel-stay-windows="$bookingConfig['hotelStayWindows'] ?? []"
                    :disabled="$bookingConfig['disabled'] ?? false"
                    :disabled-reason="$bookingConfig['disabledReason'] ?? null"
                    :invalid-date-message="$bookingConfig['invalidDateMessage'] ?? 'Choose a date during your confirmed hotel stay.'"
                    :future-message="$bookingConfig['futureMessage'] ?? 'Choose a future ferry time.'"
                    :id-prefix="'ferry_' . $ferry->id"
                />
            @else
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
                    :values="[
                        'datetime_step' => 3600,
                    ]"
                    :id-prefix="'ferry_' . $ferry->id"
                />
            @endif
        @else
            <x-ui.auth-gate-cta :login-href="route('login')" label="Log in to book" />
        @endauth
    </x-slot:footer>
</x-ui.entity-card>
