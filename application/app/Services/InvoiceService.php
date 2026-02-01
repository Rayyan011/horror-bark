<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InvoiceService
{
    public function createForBooking($booking, int $userId, float $amount): Invoice
    {
        $invoice = Invoice::create([
            'invoice_number' => $this->generateInvoiceNumber(),
            'invoiceable_type' => $booking::class,
            'invoiceable_id' => $booking->id,
            'user_id' => $userId,
            'amount' => $amount,
            'status' => 'issued',
            'issued_at' => now(),
        ]);

        $this->generatePdf($invoice);

        return $invoice;
    }

    public function generatePdf(Invoice $invoice): string
    {
        Storage::disk('local')->makeDirectory('invoices');

        $pdf = app('dompdf.wrapper')->loadView('invoices.pdf', [
            'invoice' => $invoice->load('invoiceable', 'user'),
        ]);

        $fileName = 'invoices/' . $invoice->invoice_number . '.pdf';
        Storage::disk('local')->put($fileName, $pdf->output());

        $invoice->update([
            'pdf_path' => $fileName,
        ]);

        return $fileName;
    }

    private function generateInvoiceNumber(): string
    {
        return 'INV-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
    }
}
