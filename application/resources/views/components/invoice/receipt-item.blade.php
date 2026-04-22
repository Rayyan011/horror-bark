@props([
    'invoice',
    'viewHref',
    'downloadHref',
])

<article class="theme-list-card flex flex-wrap justify-between gap-4">
    <div>
        <p class="theme-kicker">Receipt</p>
        <p class="mt-1 text-xl font-semibold text-moonlight">{{ $invoice->invoice_number }}</p>
        <p class="readable-muted">Issued: {{ optional($invoice->issued_at)->format('Y-m-d H:i') }}</p>
        <p class="readable-muted">Status: {{ ucfirst($invoice->status) }}</p>
    </div>
    <div class="text-right">
        <p class="theme-total-value !mt-0 !text-lg">MVR {{ number_format($invoice->amount, 2) }}</p>
        <div class="mt-2 flex justify-end gap-3">
            <a href="{{ $viewHref }}" class="theme-inline-link">View</a>
            <a href="{{ $downloadHref }}" class="theme-inline-link">Download PDF</a>
        </div>
    </div>
</article>
