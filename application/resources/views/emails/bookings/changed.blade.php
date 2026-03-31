<p>Hello {{ $booking->user->name }},</p>
<p>Your {{ strtolower($typeLabel) }} booking for {{ $title }} has been {{ $changeType }}.</p>
@if ($before && !empty($before['schedule']) && $changeType === 'rescheduled')
<p>Previous schedule: {{ $before['schedule'] }}</p>
@endif
<p>Current schedule: {{ $schedule }}</p>
<p>Status: {{ ucfirst($booking->status) }}</p>
@if ($invoiceUrl)
<p>Invoice: <a href="{{ $invoiceUrl }}">{{ $invoiceUrl }}</a></p>
@endif
@if ($passUrl && $booking->status !== 'canceled')
<p>Ferry pass: <a href="{{ $passUrl }}">{{ $passUrl }}</a></p>
@endif
