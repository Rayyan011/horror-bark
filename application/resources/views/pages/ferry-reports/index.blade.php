@extends('layouts.app')

@section('title', 'Ferry Passenger Reports - Horror-Bark')

@section('content')
<main class="space-y-8">
    <x-ui.section-heading title="Ferry Passenger Reports" size="xl" />

    <x-ui.alert-stack />

    <x-filters.panel
        :fields="[
            ['label' => 'Date', 'name' => 'date', 'type' => 'date', 'value' => $filters['date'] ?? now()->toDateString()],
            ['label' => 'Ferry', 'name' => 'ferry_id', 'type' => 'select', 'options' => collect($ferries)->map(fn($ferry) => ['label' => $ferry->name, 'value' => $ferry->id])->prepend(['label' => 'All ferries', 'value' => ''])->values()->all(), 'value' => $filters['ferry_id'] ?? ''],
            ['label' => 'Departure Hour', 'name' => 'hour', 'type' => 'select', 'options' => collect(range(9, 16))->map(fn($hour) => ['label' => sprintf('%02d:00', $hour), 'value' => $hour])->prepend(['label' => 'All departures', 'value' => ''])->values()->all(), 'value' => $filters['hour'] ?? ''],
        ]"
        :reset-href="route('ferry-reports.index')"
        apply-label="Run report"
        grid="grid grid-cols-1 md:grid-cols-3 gap-4"
    />

    <div class="flex justify-end">
        <x-ui.button :href="route('ferry-reports.export', array_filter([
            'date' => $filters['date'] ?? null,
            'ferry_id' => $filters['ferry_id'] ?? null,
            'hour' => $filters['hour'] ?? null,
        ], fn ($value) => !is_null($value) && $value !== ''))" variant="secondary">
            Export CSV
        </x-ui.button>
    </div>

    <x-ui.surface class="space-y-4">
        <h2 class="text-xl font-semibold">Trip Summary</h2>

        @if ($tripSummary->isEmpty())
            <p class="text-gray-300">No ferry trips matched the selected filters.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-left text-gray-200">
                    <thead class="text-xs uppercase tracking-wide text-primary-light">
                        <tr>
                            <th class="px-3 py-2">Ferry</th>
                            <th class="px-3 py-2">Departure</th>
                            <th class="px-3 py-2">Bookings</th>
                            <th class="px-3 py-2">Passengers</th>
                            <th class="px-3 py-2">Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tripSummary as $trip)
                            <tr class="border-t border-primary-light/10">
                                <td class="px-3 py-2">{{ $trip['ferry_name'] }}</td>
                                <td class="px-3 py-2">{{ $trip['departure_time']->format('Y-m-d H:i') }}</td>
                                <td class="px-3 py-2">{{ $trip['bookings_count'] }}</td>
                                <td class="px-3 py-2">{{ $trip['passenger_count'] }}</td>
                                <td class="px-3 py-2">MVR {{ number_format($trip['revenue'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-ui.surface>

    <x-ui.surface class="space-y-4">
        <h2 class="text-xl font-semibold">Passenger Manifest</h2>

        @if ($manifest->isEmpty())
            <p class="text-gray-300">No passengers found for the selected report.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-left text-gray-200">
                    <thead class="text-xs uppercase tracking-wide text-primary-light">
                        <tr>
                            <th class="px-3 py-2">Pass</th>
                            <th class="px-3 py-2">Passenger</th>
                            <th class="px-3 py-2">Ferry</th>
                            <th class="px-3 py-2">Departure</th>
                            <th class="px-3 py-2">Qty</th>
                            <th class="px-3 py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($manifest as $booking)
                            <tr class="border-t border-primary-light/10">
                                <td class="px-3 py-2">{{ $booking->pass_number ?? 'Pending issue' }}</td>
                                <td class="px-3 py-2">
                                    <div>{{ $booking->user->name }}</div>
                                    <div class="text-xs text-gray-400">{{ $booking->user->email }}</div>
                                </td>
                                <td class="px-3 py-2">{{ $booking->ferry->name }}</td>
                                <td class="px-3 py-2">{{ $booking->booking_time->format('Y-m-d H:i') }}</td>
                                <td class="px-3 py-2">{{ $booking->quantity }}</td>
                                <td class="px-3 py-2">{{ ucfirst($booking->status) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-ui.surface>
</main>
@endsection
