@props([
    'total' => 0,
    'upcoming' => 0,
    'spent' => 0,
])

<section class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <x-ui.surface padding="p-4">
        <p class="text-gray-400 text-sm">Total bookings</p>
        <p class="text-2xl font-semibold">{{ $total }}</p>
    </x-ui.surface>

    <x-ui.surface padding="p-4">
        <p class="text-gray-400 text-sm">Upcoming</p>
        <p class="text-2xl font-semibold">{{ $upcoming }}</p>
    </x-ui.surface>

    <x-ui.surface padding="p-4">
        <p class="text-gray-400 text-sm">Total spent</p>
        <p class="text-2xl font-semibold">MVR {{ number_format($spent, 2) }}</p>
    </x-ui.surface>
</section>
