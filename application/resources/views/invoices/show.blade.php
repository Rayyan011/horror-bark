@extends('layouts.app')

@section('title', 'Invoice ' . $invoice->invoice_number)

@section('content')
<main class="max-w-2xl mx-auto my-8 px-4 space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold">Invoice {{ $invoice->invoice_number }}</h1>
        <a href="{{ route('invoices.download', $invoice) }}" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
            Download PDF
        </a>
    </div>

    <section class="bg-gray-800 p-6 rounded border border-gray-700 space-y-2">
        <p><span class="text-gray-400">Issued:</span> {{ $invoice->issued_at }}</p>
        <p><span class="text-gray-400">Amount:</span> MVR {{ number_format($invoice->amount, 2) }}</p>
        <p><span class="text-gray-400">Status:</span> {{ ucfirst($invoice->status) }}</p>
    </section>

    <section class="bg-gray-800 p-6 rounded border border-gray-700 space-y-2">
        <h2 class="text-xl font-semibold">Booking Details</h2>
        <p class="text-gray-300">{{ class_basename($invoice->invoiceable_type) }} #{{ $invoice->invoiceable_id }}</p>
        @if ($invoice->invoiceable)
            <pre class="text-xs text-gray-400 whitespace-pre-wrap">{{ json_encode($invoice->invoiceable->toArray(), JSON_PRETTY_PRINT) }}</pre>
        @endif
    </section>
</main>
@endsection
