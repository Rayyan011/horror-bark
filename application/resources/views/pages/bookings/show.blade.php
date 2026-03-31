@extends('layouts.app')

@php
    $passDownloadUrl = $passDownloadUrl ?? null;
@endphp

@section('title', 'Booking Details - Horror-Bark')

@section('content')
<main class="max-w-3xl mx-auto my-8 px-4 space-y-6">
    <x-ui.section-heading title="Booking Details" size="lg" />
    <x-ui.alert-stack />

    <x-ui.surface class="space-y-2">
        <p class="text-gray-300"><span class="text-gray-400">Type:</span> {{ $type }}</p>
        <p class="text-gray-300"><span class="text-gray-400">Status:</span> {{ ucfirst($booking->status) }}</p>
        <p class="text-gray-300"><span class="text-gray-400">Quantity:</span> {{ $booking->quantity }}</p>
        @if (!is_null($booking->total_price))
            <p class="text-gray-300"><span class="text-gray-400">Total:</span> MVR {{ number_format($booking->total_price, 2) }}</p>
        @endif
        <p class="text-gray-300"><span class="text-gray-400">Self-service cutoff:</span> {{ $changeCutoffAt->format('Y-m-d H:i') }}</p>
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

    @if (!empty($passDownloadUrl))
        <x-ui.surface class="space-y-3">
            <h2 class="text-xl font-semibold">Ferry Pass</h2>
            <p class="text-gray-300">Download the issued ferry pass for boarding and manifest checks.</p>
            <x-ui.button :href="$passDownloadUrl" variant="secondary">Download ferry pass</x-ui.button>
        </x-ui.surface>
    @endif

    @if ($canSelfServiceChange)
        <x-ui.surface class="space-y-4">
            <h2 class="text-xl font-semibold">Reschedule Booking</h2>
            <form method="POST" action="{{ $rescheduleRoute }}" class="space-y-4">
                @csrf
                @method('PATCH')
                @foreach ($rescheduleFields as $field)
                    <div class="space-y-1">
                        <label class="block text-sm text-gray-300" for="{{ $field['name'] }}">{{ $field['label'] }}</label>
                        <input
                            id="{{ $field['name'] }}"
                            name="{{ $field['name'] }}"
                            type="{{ $field['type'] }}"
                            value="{{ old($field['name'], $field['value']) }}"
                            class="w-full rounded border border-gray-700 bg-gray-950 px-3 py-2 text-sm text-white"
                        >
                        @error($field['name'])
                            <p class="text-sm text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach

                <x-ui.button type="submit" variant="secondary">Reschedule booking</x-ui.button>
            </form>
        </x-ui.surface>

        <x-ui.button :href="$cancelRoute" method="PATCH" variant="danger">Cancel booking</x-ui.button>
    @elseif ($booking->status !== 'canceled')
        <x-ui.surface>
            <p class="text-sm text-amber-200">This booking is outside the 24-hour self-service change window.</p>
        </x-ui.surface>
    @endif
</main>
@endsection
