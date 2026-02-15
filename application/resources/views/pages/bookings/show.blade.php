@extends('layouts.app')

@section('title', 'Booking Details - Horror-Bark')

@section('content')
<main class="max-w-3xl mx-auto my-8 px-4 space-y-6">
    <x-ui.section-heading title="Booking Details" size="lg" />

    <x-ui.surface class="space-y-2">
        <p class="text-gray-300"><span class="text-gray-400">Type:</span> {{ $type }}</p>
        <p class="text-gray-300"><span class="text-gray-400">Status:</span> {{ ucfirst($booking->status) }}</p>
        <p class="text-gray-300"><span class="text-gray-400">Quantity:</span> {{ $booking->quantity }}</p>
        @if (!is_null($booking->total_price))
            <p class="text-gray-300"><span class="text-gray-400">Total:</span> MVR {{ number_format($booking->total_price, 2) }}</p>
        @endif
    </x-ui.surface>

    @if ($invoice)
        <x-invoice.summary-card
            :invoice-number="$invoice->invoice_number"
            :issued-at="$invoice->issued_at"
            :amount="$invoice->amount"
            :status="$invoice->status"
            :download-href="route('invoices.download', $invoice)"
        />
    @endif

    @if ($booking->status !== 'canceled')
        <x-ui.button :href="$cancelRoute" method="PATCH" variant="danger">Cancel booking</x-ui.button>
    @endif
</main>
@endsection
