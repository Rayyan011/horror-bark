@extends('layouts.app')

@section('title', 'My Bookings - Horror-Bark')

@section('content')
<main class="space-y-8">
    <x-ui.section-heading title="My Bookings" size="xl" />

    <x-ui.alert-stack />

    <x-bookings.stats
        :total="$stats['total']"
        :upcoming="$stats['upcoming']"
        :spent="$stats['spent']"
    />

    <x-filters.panel
        :fields="[
            ['label' => 'Type', 'name' => 'type', 'type' => 'select', 'options' => [
                ['label' => 'All', 'value' => ''],
                ['label' => 'Hotel', 'value' => 'hotel'],
                ['label' => 'Ferry', 'value' => 'ferry'],
                ['label' => 'Ride', 'value' => 'ride'],
                ['label' => 'Game', 'value' => 'game'],
                ['label' => 'Beach Event', 'value' => 'beach-event'],
            ], 'value' => $filters['type'] ?? ''],
            ['label' => 'Search', 'name' => 'search', 'type' => 'text', 'value' => $filters['search'] ?? '', 'placeholder' => 'Search by booking name', 'class' => 'lg:col-span-2'],
            ['label' => 'From', 'name' => 'from', 'type' => 'date', 'value' => $filters['from'] ?? ''],
            ['label' => 'To', 'name' => 'to', 'type' => 'date', 'value' => $filters['to'] ?? ''],
        ]"
        :reset-href="route('bookings.index')"
        apply-label="Apply"
        grid="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4"
    />

    <section class="space-y-4 mb-10">
        @php
            $statuses = [
                'pending' => 'Pending',
                'confirmed' => 'Confirmed',
                'canceled' => 'Canceled',
            ];
        @endphp

        @foreach($statuses as $statusKey => $statusLabel)
            @php
                $items = $bookingGroups[$statusKey] ?? collect();
            @endphp

            <x-bookings.status-group
                :status="$statusLabel"
                :items="$items"
                :open-by-default="$statusKey === 'confirmed'"
                :count="$items->count()"
            />
        @endforeach
    </section>

    <section class="space-y-4">
        <x-ui.section-heading title="Receipts" size="md" />

        <x-filters.panel
            :hidden="[
                'type' => $filters['type'] ?? '',
                'search' => $filters['search'] ?? '',
                'from' => $filters['from'] ?? '',
                'to' => $filters['to'] ?? '',
            ]"
            :fields="[
                ['label' => 'Invoice search', 'name' => 'receipt_search', 'type' => 'text', 'value' => $filters['receipt_search'] ?? '', 'placeholder' => 'Invoice number', 'class' => 'lg:col-span-2'],
                ['label' => 'Status', 'name' => 'receipt_status', 'type' => 'select', 'options' => [
                    ['label' => 'Any', 'value' => ''],
                    ['label' => 'Issued', 'value' => 'issued'],
                    ['label' => 'Canceled', 'value' => 'canceled'],
                ], 'value' => $filters['receipt_status'] ?? ''],
            ]"
            :reset-href="route('bookings.index', [
                'type' => $filters['type'] ?? null,
                'search' => $filters['search'] ?? null,
                'from' => $filters['from'] ?? null,
                'to' => $filters['to'] ?? null,
            ])"
            apply-label="Apply"
            grid="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4"
        />

        <div class="space-y-3">
            @forelse($receipts as $invoice)
                <x-invoice.receipt-item
                    :invoice="$invoice"
                    :view-href="route('invoices.show', $invoice)"
                    :download-href="route('invoices.download', $invoice)"
                />
            @empty
                <x-ui.empty-state title="No receipts found for current filters" />
            @endforelse
        </div>

        <x-ui.pagination :paginator="$receipts" />
    </section>
</main>
@endsection
