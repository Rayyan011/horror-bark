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
        <h2 class="text-xl font-semibold">Booking Details</h2>

        @if ($invoice->invoiceable)
            @php $booking = $invoice->invoiceable; @endphp

            <dl class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
                <dt class="text-gray-400">Type</dt>
                <dd class="text-gray-200">{{ str_replace('Booking', ' Booking', class_basename($invoice->invoiceable_type)) }}</dd>

                <dt class="text-gray-400">Booking ID</dt>
                <dd class="text-gray-200">#{{ $booking->id }}</dd>

                @if ($booking->ferry ?? null)
                    <dt class="text-gray-400">Ferry</dt>
                    <dd class="text-gray-200">{{ $booking->ferry->name }}</dd>
                @elseif ($booking->ride ?? null)
                    <dt class="text-gray-400">Ride</dt>
                    <dd class="text-gray-200">{{ $booking->ride->name }}</dd>
                @elseif ($booking->game ?? null)
                    <dt class="text-gray-400">Game</dt>
                    <dd class="text-gray-200">{{ $booking->game->name }}</dd>
                @elseif ($booking->room ?? null)
                    <dt class="text-gray-400">Hotel / Room</dt>
                    <dd class="text-gray-200">{{ $booking->room->hotel->name ?? 'N/A' }} — {{ $booking->room->name ?? 'N/A' }}</dd>
                @elseif ($booking->beachEvent ?? null)
                    <dt class="text-gray-400">Beach Event</dt>
                    <dd class="text-gray-200">{{ $booking->beachEvent->name }}</dd>
                @endif

                @if ($booking->start_date ?? null)
                    <dt class="text-gray-400">Check-in</dt>
                    <dd class="text-gray-200">{{ \Carbon\Carbon::parse($booking->start_date)->format('M d, Y') }}</dd>
                    <dt class="text-gray-400">Check-out</dt>
                    <dd class="text-gray-200">{{ \Carbon\Carbon::parse($booking->end_date)->format('M d, Y') }}</dd>
                @elseif ($booking->booking_time ?? null)
                    <dt class="text-gray-400">Date & Time</dt>
                    <dd class="text-gray-200">{{ \Carbon\Carbon::parse($booking->booking_time)->format('M d, Y — h:i A') }}</dd>
                @elseif ($booking->booking_date ?? null)
                    <dt class="text-gray-400">Event Date</dt>
                    <dd class="text-gray-200">{{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}</dd>
                @endif

                <dt class="text-gray-400">Quantity</dt>
                <dd class="text-gray-200">{{ $booking->quantity }}</dd>

                <dt class="text-gray-400">Total Price</dt>
                <dd class="text-gray-200 font-semibold">MVR {{ number_format($booking->total_price, 2) }}</dd>

                <dt class="text-gray-400">Status</dt>
                <dd>
                    <span @class([
                        'inline-block px-2 py-0.5 rounded text-xs font-medium',
                        'bg-yellow-900/50 text-yellow-300' => $booking->status === 'pending',
                        'bg-green-900/50 text-green-300' => $booking->status === 'confirmed',
                        'bg-red-900/50 text-red-300' => $booking->status === 'canceled',
                    ])>
                        {{ ucfirst($booking->status) }}
                    </span>
                </dd>
            </dl>
        @else
            <p class="text-gray-400 text-sm">Booking details are no longer available.</p>
        @endif
    </x-ui.surface>
</main>
@endsection
