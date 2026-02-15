@props([
    'invoice',
    'customer',
])

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
    </style>
</head>
<body>
    <div class="header">
        <h1>Invoice {{ $invoice->invoice_number }}</h1>
        <p><span class="label">Issued:</span> {{ $invoice->issued_at }}</p>
        <p><span class="label">Customer:</span> {{ $customer->name }} ({{ $customer->email }})</p>
    </div>

    <div class="section">
        {{ $summary }}
    </div>

    <div class="section">
        {{ $slot }}
    </div>
</body>
</html>
