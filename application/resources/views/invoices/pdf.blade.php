<x-print.invoice-layout :invoice="$invoice" :customer="$invoice->user">
    <x-slot:summary>
        <h2>Summary</h2>
        <p><span class="label">Amount:</span> MVR {{ number_format($invoice->amount, 2) }}</p>
        <p><span class="label">Status:</span> {{ ucfirst($invoice->status) }}</p>
    </x-slot:summary>

    <h2>Booking</h2>
    <x-print.invoice-table :rows="[
        ['label' => 'Type', 'value' => class_basename($invoice->invoiceable_type)],
        ['label' => 'Booking ID', 'value' => $invoice->invoiceable_id],
    ]" />
</x-print.invoice-layout>
