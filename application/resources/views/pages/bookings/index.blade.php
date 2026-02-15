@extends('layouts.app')

@section('title', 'My Bookings - Horror-Bark')

@section('content')
<main class="container mx-auto my-8 px-4">
    <h1 class="text-4xl font-bold mb-6 horror-font">My Bookings</h1>

    @if (session('status'))
        <div class="mb-4 text-green-300 text-sm">{{ session('status') }}</div>
    @endif

    <section class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-gray-800 p-4 rounded border border-gray-700">
            <p class="text-gray-400 text-sm">Total bookings</p>
            <p class="text-2xl font-semibold">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-gray-800 p-4 rounded border border-gray-700">
            <p class="text-gray-400 text-sm">Upcoming</p>
            <p class="text-2xl font-semibold">{{ $stats['upcoming'] }}</p>
        </div>
        <div class="bg-gray-800 p-4 rounded border border-gray-700">
            <p class="text-gray-400 text-sm">Total spent</p>
            <p class="text-2xl font-semibold">${{ number_format($stats['spent'], 2) }}</p>
        </div>
    </section>

    <form method="GET" class="bg-gray-800 p-4 rounded border border-gray-700 mb-8 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm mb-1" for="type">Type</label>
                <select id="type" name="type" class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white">
                    <option value="">All</option>
                    <option value="hotel" @selected(($filters['type'] ?? '') === 'hotel')>Hotel</option>
                    <option value="ferry" @selected(($filters['type'] ?? '') === 'ferry')>Ferry</option>
                    <option value="ride" @selected(($filters['type'] ?? '') === 'ride')>Ride</option>
                    <option value="game" @selected(($filters['type'] ?? '') === 'game')>Game</option>
                    <option value="beach-event" @selected(($filters['type'] ?? '') === 'beach-event')>Beach Event</option>
                </select>
            </div>
            <div class="lg:col-span-2">
                <label class="block text-sm mb-1" for="search">Search</label>
                <input id="search" name="search" type="text" value="{{ $filters['search'] ?? '' }}"
                    placeholder="Search by booking name"
                    class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white" />
            </div>
            <div>
                <label class="block text-sm mb-1" for="from">From</label>
                <input id="from" name="from" type="date" value="{{ $filters['from'] ?? '' }}"
                    class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white" />
            </div>
            <div>
                <label class="block text-sm mb-1" for="to">To</label>
                <input id="to" name="to" type="date" value="{{ $filters['to'] ?? '' }}"
                    class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white" />
            </div>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Apply</button>
            <a href="{{ route('bookings.index') }}" class="px-4 py-2 rounded border border-gray-600 text-gray-200 hover:text-white">Reset</a>
        </div>
    </form>

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
            <details class="bg-gray-800 rounded border border-gray-700" @if($statusKey === 'confirmed') open @endif>
                <summary class="cursor-pointer list-none p-4 flex items-center justify-between">
                    <span class="text-xl font-semibold">{{ $statusLabel }}</span>
                    <span class="text-sm text-gray-400">{{ $items->count() }} booking(s)</span>
                </summary>
                <div class="px-4 pb-4 space-y-4">
                    @forelse($items as $booking)
                        <article class="bg-gray-900 p-4 rounded border border-gray-700">
                            <div class="flex flex-wrap justify-between gap-4">
                                <div>
                                    <p class="text-xs uppercase tracking-wide text-gray-400">{{ $booking['type_label'] }}</p>
                                    <p class="text-white font-semibold">{{ $booking['title'] }}</p>
                                    <p class="text-gray-400 text-sm">{{ $booking['subtitle'] }}</p>
                                    <p class="text-gray-400 text-sm">Schedule: {{ $booking['schedule'] }}</p>
                                    <p class="text-gray-400 text-sm">Quantity: {{ $booking['quantity'] }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-gray-300 text-sm">Total: ${{ number_format($booking['total_price'], 2) }}</p>
                                    <p class="text-gray-400 text-sm">Status: {{ ucfirst($booking['status']) }}</p>
                                    <a href="{{ $booking['detail_url'] }}" class="text-sm text-red-300 hover:text-red-200">View details</a>
                                    @if ($booking['can_cancel'])
                                        <form method="POST" action="{{ $booking['cancel_url'] }}" class="mt-2">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="bg-red-700 text-white px-3 py-1 rounded hover:bg-red-600">
                                                Cancel
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @empty
                        <p class="text-gray-400 text-sm">No {{ strtolower($statusLabel) }} bookings found for current filters.</p>
                    @endforelse
                </div>
            </details>
        @endforeach
    </section>

    <section>
        <h2 class="text-2xl font-bold mb-4">Receipts</h2>
        <form method="GET" class="bg-gray-800 p-4 rounded border border-gray-700 mb-6 space-y-4">
            <input type="hidden" name="type" value="{{ $filters['type'] ?? '' }}">
            <input type="hidden" name="search" value="{{ $filters['search'] ?? '' }}">
            <input type="hidden" name="from" value="{{ $filters['from'] ?? '' }}">
            <input type="hidden" name="to" value="{{ $filters['to'] ?? '' }}">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="lg:col-span-2">
                    <label class="block text-sm mb-1" for="receipt_search">Invoice search</label>
                    <input id="receipt_search" name="receipt_search" type="text" value="{{ $filters['receipt_search'] ?? '' }}"
                        placeholder="Invoice number"
                        class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white" />
                </div>
                <div>
                    <label class="block text-sm mb-1" for="receipt_status">Status</label>
                    <select id="receipt_status" name="receipt_status" class="w-full px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white">
                        <option value="">Any</option>
                        <option value="issued" @selected(($filters['receipt_status'] ?? '') === 'issued')>Issued</option>
                        <option value="canceled" @selected(($filters['receipt_status'] ?? '') === 'canceled')>Canceled</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Apply</button>
                <a href="{{ route('bookings.index', [
                    'type' => $filters['type'] ?? null,
                    'search' => $filters['search'] ?? null,
                    'from' => $filters['from'] ?? null,
                    'to' => $filters['to'] ?? null,
                ]) }}" class="px-4 py-2 rounded border border-gray-600 text-gray-200 hover:text-white">Reset Receipts</a>
            </div>
        </form>

        <div class="space-y-3">
            @forelse($receipts as $invoice)
                <article class="bg-gray-800 p-4 rounded border border-gray-700 flex flex-wrap justify-between gap-4">
                    <div>
                        <p class="text-white font-semibold">{{ $invoice->invoice_number }}</p>
                        <p class="text-gray-400 text-sm">Issued: {{ optional($invoice->issued_at)->format('Y-m-d H:i') }}</p>
                        <p class="text-gray-400 text-sm">Status: {{ ucfirst($invoice->status) }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-300 text-sm mb-2">Amount: ${{ number_format($invoice->amount, 2) }}</p>
                        <a href="{{ route('invoices.show', $invoice) }}" class="text-sm text-red-300 hover:text-red-200">View</a>
                        <a href="{{ route('invoices.download', $invoice) }}" class="ml-3 text-sm text-red-300 hover:text-red-200">Download PDF</a>
                    </div>
                </article>
            @empty
                <p class="text-gray-400">No receipts found for current filters.</p>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $receipts->links() }}
        </div>
    </section>
</main>
@endsection
