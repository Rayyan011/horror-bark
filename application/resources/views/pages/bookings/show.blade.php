@extends('layouts.app')

@section('title', 'Booking Details - Horror-Bark')

@section('content')
<main class="max-w-3xl mx-auto my-8 px-4 space-y-6">
    <h1 class="text-3xl font-bold">Booking Details</h1>

    <section class="bg-gray-800 p-6 rounded border border-gray-700 space-y-2">
        <p class="text-gray-300"><span class="text-gray-400">Type:</span> {{ $type }}</p>
        <p class="text-gray-300"><span class="text-gray-400">Status:</span> {{ ucfirst($booking->status) }}</p>
        <p class="text-gray-300"><span class="text-gray-400">Quantity:</span> {{ $booking->quantity }}</p>
        @if (!is_null($booking->total_price))
            <p class="text-gray-300"><span class="text-gray-400">Total:</span> MVR {{ number_format($booking->total_price, 2) }}</p>
        @endif
    </section>

    @if ($invoice)
        <section class="bg-gray-800 p-6 rounded border border-gray-700 space-y-2">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold">Invoice</h2>
                <a href="{{ route('invoices.download', $invoice) }}" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                    Download PDF
                </a>
            </div>
            <p class="text-gray-300"><span class="text-gray-400">Invoice #:</span> {{ $invoice->invoice_number }}</p>
            <p class="text-gray-300"><span class="text-gray-400">Issued:</span> {{ $invoice->issued_at }}</p>
        </section>
    @endif

    @if ($booking->status !== 'canceled')
        <form method="POST" action="{{ $cancelRoute }}" class="mt-2">
            @csrf
            @method('PATCH')
            <button type="submit" class="bg-red-700 text-white px-4 py-2 rounded hover:bg-red-600">
                Cancel booking
            </button>
        </form>
    @endif
</main>
@endsection
