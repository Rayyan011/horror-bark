@extends('layouts.app')

@section('title', 'Review & Payment - Horror-Bark')

@section('content')
<main class="mx-auto max-w-5xl space-y-8">
    <section class="rounded-[2rem] border border-primary-light/15 bg-[radial-gradient(circle_at_top,_rgba(156,107,255,0.16),_transparent_58%),linear-gradient(135deg,_rgba(12,11,22,0.98),_rgba(18,13,28,0.93))] px-6 py-10 shadow-[0_30px_80px_rgba(0,0,0,0.35)] sm:px-10">
        <div class="max-w-4xl space-y-4">
            <p class="theme-kicker">Booking Confirmation</p>
            <h1 class="font-display text-4xl italic leading-none text-moonlight sm:text-5xl">Review the booking, then pass through the demo payment gate.</h1>
            <p class="readable-copy max-w-3xl">
                This assignment build does not process real payments. The form below simulates a payment step, confirms the booking,
                and issues the same invoice and receipt flow used by the rest of the customer portal.
            </p>
        </div>
    </section>

    <x-ui.alert-stack />

    <div class="grid gap-6 lg:grid-cols-[1.05fr,0.95fr]">
        <x-ui.surface class="space-y-5">
            <div class="space-y-2">
                <p class="theme-kicker">{{ $checkout['summary']['type_label'] }}</p>
                <h2 class="catalog-card-title">{{ $checkout['summary']['title'] }}</h2>
                <p class="readable-copy">{{ $checkout['summary']['subtitle'] }}</p>
                <p class="readable-muted">Scheduled for {{ $checkout['summary']['schedule_label'] }}</p>
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                @foreach ($checkout['summary']['line_items'] as $line)
                    <div class="theme-detail-card">
                        <p class="theme-label">{{ $line['label'] }}</p>
                        <p class="theme-detail-value">{{ $line['value'] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="grid gap-3 sm:grid-cols-3">
                <div class="theme-detail-card">
                    <p class="theme-label">{{ $checkout['summary']['quantity_label'] }}</p>
                    <p class="theme-detail-value">{{ $checkout['summary']['quantity'] }}</p>
                </div>
                <div class="theme-detail-card">
                    <p class="theme-label">{{ $checkout['summary']['unit_label'] }}</p>
                    <p class="theme-detail-value">MVR {{ number_format($checkout['summary']['unit_price'], 2) }}</p>
                </div>
                <div class="theme-total-card">
                    <p class="theme-label">Total Due</p>
                    <p class="theme-total-value">MVR {{ number_format($checkout['summary']['total_price'], 2) }}</p>
                </div>
            </div>

            <p class="readable-muted">
                Once the demo gateway is confirmed, the booking becomes active immediately and the customer portal will expose the booking record and receipt.
            </p>
        </x-ui.surface>

        <x-ui.surface class="space-y-5">
            <div class="space-y-2">
                <p class="theme-kicker">Demo Payment Gateway</p>
                <h2 class="text-3xl gothic-title">Payment Details</h2>
                <p class="readable-copy">
                    Enter placeholder payment details. No real transaction, card tokenisation, or external gateway call is performed.
                </p>
            </div>

            <x-ui.form :action="route('checkout.confirm', $token)" class="space-y-4">
                <x-ui.select
                    label="Payment Method"
                    name="payment_method"
                    :options="[
                        ['label' => 'Ghost Card', 'value' => 'ghost_card'],
                        ['label' => 'Moonwire Transfer', 'value' => 'moonwire_transfer'],
                        ['label' => 'Crypt Vault', 'value' => 'crypt_vault'],
                    ]"
                    :value="old('payment_method', 'ghost_card')"
                />

                <x-ui.field
                    label="Cardholder Name"
                    name="cardholder_name"
                    :value="old('cardholder_name', auth()->user()->name)"
                    placeholder="Name on card"
                    required
                />

                <x-ui.field
                    label="Card Number"
                    name="card_number"
                    :value="old('card_number', '4242424242424242')"
                    placeholder="4242424242424242"
                    required
                />

                <div class="grid gap-4 sm:grid-cols-3">
                    <x-ui.field
                        label="Expiry Month"
                        name="expiry_month"
                        :value="old('expiry_month', '12')"
                        placeholder="MM"
                        required
                    />
                    <x-ui.field
                        label="Expiry Year"
                        name="expiry_year"
                        :value="old('expiry_year', '29')"
                        placeholder="YY"
                        required
                    />
                    <x-ui.field
                        label="Security Code"
                        name="security_code"
                        :value="old('security_code', '123')"
                        placeholder="CVV"
                        required
                    />
                </div>

                <div
                    class="theme-qr-panel"
                    data-qr-generator
                    data-qr-payload="{{ e($checkout['qr_payload']) }}"
                    data-qr-reference="{{ $checkout['qr_reference'] }}"
                >
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="space-y-1">
                            <p class="theme-kicker">Scan Option</p>
                            <h3 class="text-xl font-display uppercase tracking-[0.12em] text-moonlight">Generate Payment QR</h3>
                            <p class="readable-muted">
                                Use this demonstration QR if the customer wants a scan-style payment handoff instead of manually typing the placeholder details.
                            </p>
                        </div>

                        <div class="theme-qr-reference">
                            <span class="theme-label">Reference</span>
                            <span class="theme-detail-value">{{ $checkout['qr_reference'] }}</span>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <x-ui.button type="button" variant="secondary" data-qr-button>Generate Payment QR</x-ui.button>
                        <a
                            href="#"
                            class="hidden"
                            data-qr-download
                            download="horror-bark-demo-payment-{{ strtolower($checkout['qr_reference']) }}.svg"
                        >
                            Download QR
                        </a>
                    </div>

                    <div class="theme-qr-output hidden" data-qr-output></div>
                    <p class="hidden readable-muted" data-qr-status></p>
                </div>

                <label class="theme-checkbox-row">
                    <input
                        type="checkbox"
                        name="acknowledge_demo"
                        value="1"
                        class="h-4 w-4 rounded border-primary-light/30 bg-background-dark/80 text-primary-light focus:ring-primary-light/40"
                        @checked(old('acknowledge_demo'))
                    >
                    <span class="readable-muted">
                        I understand this is a demonstration payment screen and no real payment will be taken.
                    </span>
                </label>

                <div class="flex flex-wrap gap-3 pt-2">
                    <x-ui.button type="submit" variant="primary">Simulate Payment & Confirm</x-ui.button>
                    <x-ui.button :href="$checkout['return_url'] ?? route('bookings.index')" variant="ghost">Return to catalog</x-ui.button>
                </div>
            </x-ui.form>
        </x-ui.surface>
    </div>
</main>
@endsection
