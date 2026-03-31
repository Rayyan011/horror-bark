<p>Hello {{ $booking->user->name }},</p>
<p>Your {{ strtolower($typeLabel) }} booking for {{ $title }} is confirmed.</p>
<p>Schedule: {{ $schedule }}</p>
<p>Total: MVR {{ number_format((float) $booking->total_price, 2) }}</p>
@if ($invoiceUrl)
<p>Invoice: <a href="{{ $invoiceUrl }}">{{ $invoiceUrl }}</a></p>
@endif
@if ($passUrl)
<p>Ferry pass: <a href="{{ $passUrl }}">{{ $passUrl }}</a></p>
@endif
