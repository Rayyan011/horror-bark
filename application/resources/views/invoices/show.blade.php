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

    <x-ui.surface class="space-y-4">
        <h2 class="text-xl font-semibold">Invoice Details</h2>

        <dl class="grid gap-3 sm:grid-cols-2">
            <div>
                <dt class="theme-detail-label">Invoice ID</dt>
                <dd class="theme-detail-value">#{{ $invoice->id }}</dd>
            </div>
            <div>
                <dt class="theme-detail-label">Booking Type</dt>
                <dd class="theme-detail-value">{{ class_basename($invoice->invoiceable_type) }}</dd>
            </div>
            <div>
                <dt class="theme-detail-label">Invoice Number</dt>
                <dd class="theme-detail-value">{{ $invoice->invoice_number }}</dd>
            </div>
            <div>
                <dt class="theme-detail-label">Customer</dt>
                <dd class="theme-detail-value">{{ $invoice->user?->name ?? 'Customer' }}</dd>
            </div>
        </dl>
    </x-ui.surface>
</main>
@endsection
