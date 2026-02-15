@props([
    'invoiceNumber',
    'issuedAt',
    'amount',
    'status',
    'downloadHref' => null,
])

<x-ui.surface class="space-y-2">
    <div class="flex items-center justify-between gap-3">
        <h2 class="text-xl font-semibold">Invoice {{ $invoiceNumber }}</h2>

        @if ($downloadHref)
            <x-ui.button :href="$downloadHref" variant="primary">Download PDF</x-ui.button>
        @endif
    </div>

    <p><span class="text-gray-400">Issued:</span> {{ $issuedAt }}</p>
    <p><span class="text-gray-400">Amount:</span> MVR {{ number_format($amount, 2) }}</p>
    <p><span class="text-gray-400">Status:</span> {{ ucfirst($status) }}</p>
</x-ui.surface>
