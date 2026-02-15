@extends('layouts.app')

@section('title', 'Invoice ' . $invoice->invoice_number)

@section('content')
<main class="max-w-2xl mx-auto my-8 px-4 space-y-6">
    <x-invoice.summary-card
        :invoice-number="$invoice->invoice_number"
        :issued-at="$invoice->issued_at"
        :amount="$invoice->amount"
        :status="$invoice->status"
        :download-href="route('invoices.download', $invoice)"
    />

    <x-ui.surface class="space-y-2">
        <h2 class="text-xl font-semibold">Booking Details</h2>
        <p class="text-gray-300">{{ class_basename($invoice->invoiceable_type) }} #{{ $invoice->invoiceable_id }}</p>
        @if ($invoice->invoiceable)
            <pre class="text-xs text-gray-400 whitespace-pre-wrap">{{ json_encode($invoice->invoiceable->toArray(), JSON_PRETTY_PRINT) }}</pre>
        @endif
    </x-ui.surface>
</main>
@endsection
