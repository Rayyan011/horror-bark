<p>Hello {{ $booking->user->name }},</p>
<p>This is your 24-hour reminder for the upcoming {{ strtolower($typeLabel) }} booking for {{ $title }}.</p>
<p>Schedule: {{ $schedule }}</p>
@if ($invoiceUrl)
<p>Invoice: <a href="{{ $invoiceUrl }}">{{ $invoiceUrl }}</a></p>
@endif
@if ($passUrl)
<p>Ferry pass: <a href="{{ $passUrl }}">{{ $passUrl }}</a></p>
@endif
