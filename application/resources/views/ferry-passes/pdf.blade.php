<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ferry Pass {{ $booking->pass_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #111827; margin: 32px; }
        .ticket { border: 2px solid #111827; border-radius: 16px; padding: 24px; }
        .heading { font-size: 28px; margin: 0 0 8px; }
        .subheading { color: #4b5563; margin: 0 0 24px; }
        .grid { width: 100%; border-collapse: collapse; margin-top: 16px; }
        .grid td { padding: 10px 0; border-bottom: 1px solid #d1d5db; }
        .label { font-weight: bold; width: 180px; }
        .footer { margin-top: 24px; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
    <section class="ticket">
        <h1 class="heading">Horror Bark Ferry Pass</h1>
        <p class="subheading">Boarding document for island transfer access</p>

        <table class="grid">
            <tr>
                <td class="label">Pass Number</td>
                <td>{{ $booking->pass_number }}</td>
            </tr>
            <tr>
                <td class="label">Passenger</td>
                <td>{{ $booking->user->name }} ({{ $booking->user->email }})</td>
            </tr>
            <tr>
                <td class="label">Ferry</td>
                <td>{{ $booking->ferry->name }}</td>
            </tr>
            <tr>
                <td class="label">Destination</td>
                <td>{{ $booking->ferry->island?->name ?? 'Island transfer' }}</td>
            </tr>
            <tr>
                <td class="label">Departure</td>
                <td>{{ $booking->booking_time->format('Y-m-d H:i') }}</td>
            </tr>
            <tr>
                <td class="label">Passengers</td>
                <td>{{ $booking->quantity }}</td>
            </tr>
            <tr>
                <td class="label">Status</td>
                <td>{{ ucfirst($booking->status) }}</td>
            </tr>
        </table>

        <p class="footer">Present this pass with a matching booking and identification at the ferry checkpoint.</p>
    </section>
</body>
</html>
