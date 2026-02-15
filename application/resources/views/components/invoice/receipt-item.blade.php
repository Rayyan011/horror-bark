@props([
    'invoice',
    'viewHref',
    'downloadHref',
])

<article class="bg-gray-800 p-4 rounded border border-gray-700 flex flex-wrap justify-between gap-4">
    <div>
        <p class="text-white font-semibold">{{ $invoice->invoice_number }}</p>
        <p class="text-gray-400 text-sm">Issued: {{ optional($invoice->issued_at)->format('Y-m-d H:i') }}</p>
        <p class="text-gray-400 text-sm">Status: {{ ucfirst($invoice->status) }}</p>
    </div>
    <div class="text-right">
        <p class="text-gray-300 text-sm mb-2">Amount: MVR {{ number_format($invoice->amount, 2) }}</p>
        <a href="{{ $viewHref }}" class="text-sm text-red-300 hover:text-red-200">View</a>
        <a href="{{ $downloadHref }}" class="ml-3 text-sm text-red-300 hover:text-red-200">Download PDF</a>
    </div>
</article>
