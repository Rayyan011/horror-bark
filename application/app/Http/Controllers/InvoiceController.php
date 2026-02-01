<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    public function show(Request $request, Invoice $invoice)
    {
        $this->authorizeInvoice($request, $invoice);

        return view('invoices.show', [
            'invoice' => $invoice->load('invoiceable', 'user'),
        ]);
    }

    public function download(Request $request, Invoice $invoice, InvoiceService $invoiceService)
    {
        $this->authorizeInvoice($request, $invoice);

        $path = $invoice->pdf_path;
        if (!$path || !Storage::disk('local')->exists($path)) {
            $path = $invoiceService->generatePdf($invoice);
        }

        return Storage::disk('local')->download($path);
    }

    private function authorizeInvoice(Request $request, Invoice $invoice): void
    {
        abort_unless($request->user()->id === $invoice->user_id, 403);
    }
}
