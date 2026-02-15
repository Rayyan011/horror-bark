<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #111; }
        .header { margin-bottom: 24px; }
        .section { margin-bottom: 16px; }
        .label { color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f5f5f5; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Invoice {{ $invoice->invoice_number }}</h1>
        <p><span class="label">Issued:</span> {{ $invoice->issued_at }}</p>
        <p><span class="label">Customer:</span> {{ $invoice->user->name }} ({{ $invoice->user->email }})</p>
    </div>

    <div class="section">
        <h2>Summary</h2>
        <p><span class="label">Amount:</span> MVR {{ number_format($invoice->amount, 2) }}</p>
        <p><span class="label">Status:</span> {{ ucfirst($invoice->status) }}</p>
    </div>

    <div class="section">
        <h2>Booking</h2>
        <table>
            <tr>
                <th>Type</th>
                <td>{{ class_basename($invoice->invoiceable_type) }}</td>
            </tr>
            <tr>
                <th>Booking ID</th>
                <td>{{ $invoice->invoiceable_id }}</td>
            </tr>
        </table>
    </div>
</body>
</html>
