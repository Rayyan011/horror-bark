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
        <p class="readable-copy">{{ class_basename($invoice->invoiceable_type) }} #{{ $invoice->invoiceable_id }}</p>
        @if ($invoice->invoiceable)
            <pre class="overflow-x-auto rounded-sm border border-primary-light/10 bg-background-dark/60 p-4 text-xs text-primary-light/75 whitespace-pre-wrap">{{ json_encode($invoice->invoiceable->toArray(), JSON_PRETTY_PRINT) }}</pre>
        @endif
    </x-ui.surface>
</main>
@endsection
