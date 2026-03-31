@extends('layouts.app')

@section('title', $title.' - Horror-Bark')

@section('content')
<main class="max-w-7xl mx-auto my-8 px-4 space-y-6">
    <x-ui.section-heading :title="$title" size="lg" />

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <x-ui.surface><p class="text-sm text-gray-400">Bookings</p><p class="text-2xl text-white">{{ number_format($summary['bookings']) }}</p></x-ui.surface>
        <x-ui.surface><p class="text-sm text-gray-400">Guests</p><p class="text-2xl text-white">{{ number_format($summary['passengers']) }}</p></x-ui.surface>
        <x-ui.surface><p class="text-sm text-gray-400">Revenue</p><p class="text-2xl text-white">MVR {{ number_format($summary['revenue'], 2) }}</p></x-ui.surface>
        <x-ui.surface><p class="text-sm text-gray-400">Cancellations</p><p class="text-2xl text-white">{{ number_format($summary['cancellations']) }}</p></x-ui.surface>
    </div>

    <x-filters.panel
        :fields="array_values(array_filter([
            ['label' => 'From', 'name' => 'from', 'type' => 'date', 'value' => $filters['from'] ?? ''],
            ['label' => 'To', 'name' => 'to', 'type' => 'date', 'value' => $filters['to'] ?? ''],
            ['label' => 'Status', 'name' => 'status', 'type' => 'select', 'options' => [
                ['label' => 'Any', 'value' => ''],
                ['label' => 'Pending', 'value' => 'pending'],
                ['label' => 'Confirmed', 'value' => 'confirmed'],
                ['label' => 'Canceled', 'value' => 'canceled'],
            ], 'value' => $filters['status'] ?? ''],
            $showTypeFilter ? ['label' => 'Type', 'name' => 'type', 'type' => 'select', 'options' => [
                ['label' => 'All', 'value' => ''],
                ['label' => 'Hotel', 'value' => 'hotel'],
                ['label' => 'Ferry', 'value' => 'ferry'],
                ['label' => 'Ride', 'value' => 'ride'],
                ['label' => 'Game', 'value' => 'game'],
                ['label' => 'Beach Event', 'value' => 'beach-event'],
            ], 'value' => $filters['type'] ?? ''] : null,
            $islands->isNotEmpty() ? ['label' => 'Island', 'name' => 'island_id', 'type' => 'select', 'options' => array_merge([['label' => 'Any', 'value' => '']], $islands->map(fn ($island) => ['label' => $island->name, 'value' => (string) $island->id])->all()), 'value' => (string) ($filters['island_id'] ?? '')] : null,
        ]))"
        :reset-href="request()->url()"
        apply-label="Apply"
        grid="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4"
    />

    <div class="flex justify-end">
        <x-ui.button :href="$exportUrl" variant="secondary">Export CSV</x-ui.button>
    </div>

    <x-ui.surface class="overflow-x-auto">
        <table class="min-w-full text-sm text-left text-gray-300">
            <thead>
                <tr class="border-b border-gray-700 text-xs uppercase tracking-wide text-gray-400">
                    <th class="px-3 py-3">Type</th>
                    <th class="px-3 py-3">Listing</th>
                    <th class="px-3 py-3">Customer</th>
                    <th class="px-3 py-3">Island</th>
                    <th class="px-3 py-3">Schedule</th>
                    <th class="px-3 py-3">Qty</th>
                    <th class="px-3 py-3">Total</th>
                    <th class="px-3 py-3">Status</th>
                    <th class="px-3 py-3">Invoice</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr class="border-b border-gray-800">
                        <td class="px-3 py-3">{{ $row['type_label'] }}</td>
                        <td class="px-3 py-3">{{ $row['title'] }}</td>
                        <td class="px-3 py-3">{{ $row['customer_name'] }}<div class="text-xs text-gray-500">{{ $row['customer_email'] }}</div></td>
                        <td class="px-3 py-3">{{ $row['island_name'] ?? 'N/A' }}</td>
                        <td class="px-3 py-3">{{ $row['schedule'] }}</td>
                        <td class="px-3 py-3">{{ $row['quantity'] }}</td>
                        <td class="px-3 py-3">MVR {{ number_format($row['total_price'], 2) }}</td>
                        <td class="px-3 py-3">{{ ucfirst($row['status']) }}</td>
                        <td class="px-3 py-3">{{ $row['invoice_number'] ?? 'N/A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-3 py-6 text-center text-gray-400">No report rows match the current filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-ui.surface>
</main>
@endsection
