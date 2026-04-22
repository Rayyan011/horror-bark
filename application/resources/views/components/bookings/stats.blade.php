@props([
    'total' => 0,
    'upcoming' => 0,
    'spent' => 0,
])

<section class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <x-ui.surface padding="p-4" class="space-y-2">
        <p class="theme-kicker">Total bookings</p>
        <p class="theme-total-value !mt-0">{{ $total }}</p>
    </x-ui.surface>

    <x-ui.surface padding="p-4" class="space-y-2">
        <p class="theme-kicker">Upcoming</p>
        <p class="theme-total-value !mt-0">{{ $upcoming }}</p>
    </x-ui.surface>

    <x-ui.surface padding="p-4" class="space-y-2">
        <p class="theme-kicker">Total spent</p>
        <p class="theme-total-value !mt-0">MVR {{ number_format($spent, 2) }}</p>
    </x-ui.surface>
</section>
