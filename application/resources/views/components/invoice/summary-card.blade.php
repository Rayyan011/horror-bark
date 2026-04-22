@props([
    'invoiceNumber',
    'issuedAt',
    'amount',
    'status',
    'downloadHref' => null,
])

<x-ui.surface class="space-y-4">
    <div class="flex items-center justify-between gap-3">
        <h2 class="text-xl font-semibold">Invoice {{ $invoiceNumber }}</h2>

        @if ($downloadHref)
            <x-ui.button :href="$downloadHref" variant="primary">Download PDF</x-ui.button>
        @endif
    </div>

    <div class="grid gap-3 sm:grid-cols-3">
        <div class="theme-detail-card">
            <p class="theme-label">Issued</p>
            <p class="theme-detail-value">{{ $issuedAt }}</p>
        </div>
        <div class="theme-detail-card">
            <p class="theme-label">Amount</p>
            <p class="theme-detail-value">MVR {{ number_format($amount, 2) }}</p>
        </div>
        <div class="theme-detail-card">
            <p class="theme-label">Status</p>
            <p class="theme-detail-value">{{ ucfirst($status) }}</p>
        </div>
    </div>
</x-ui.surface>
