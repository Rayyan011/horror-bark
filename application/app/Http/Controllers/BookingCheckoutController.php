<?php

namespace App\Http\Controllers;

use App\Models\BeachEvent;
use App\Models\Ferry;
use App\Models\Game;
use App\Models\Ride;
use App\Models\Room;
use App\Services\BookingCheckoutService;
use App\Support\BookingSupport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BookingCheckoutController extends Controller
{
    private const SESSION_KEY = 'booking_checkout';

    public function prepareHotel(Request $request, Room $room, BookingCheckoutService $checkoutService): RedirectResponse
    {
        $checkout = $checkoutService->prepareHotel($request->user(), $room, $request->all());

        return $this->storeCheckoutAndRedirect($request, $checkout);
    }

    public function prepareFerry(Request $request, Ferry $ferry, BookingCheckoutService $checkoutService): RedirectResponse
    {
        $checkout = $checkoutService->prepareFerry($request->user(), $ferry, $request->all());

        return $this->storeCheckoutAndRedirect($request, $checkout);
    }

    public function prepareRide(Request $request, Ride $ride, BookingCheckoutService $checkoutService): RedirectResponse
    {
        $checkout = $checkoutService->prepareRide($request->user(), $ride, $request->all());

        return $this->storeCheckoutAndRedirect($request, $checkout);
    }

    public function prepareGame(Request $request, Game $game, BookingCheckoutService $checkoutService): RedirectResponse
    {
        $checkout = $checkoutService->prepareGame($request->user(), $game, $request->all());

        return $this->storeCheckoutAndRedirect($request, $checkout);
    }

    public function prepareBeachEvent(Request $request, BeachEvent $beachEvent, BookingCheckoutService $checkoutService): RedirectResponse
    {
        $checkout = $checkoutService->prepareBeachEvent($request->user(), $beachEvent, $request->all());

        return $this->storeCheckoutAndRedirect($request, $checkout);
    }

    public function show(Request $request, string $token): View
    {
        return view('pages.checkout.show', [
            'checkout' => $this->resolveCheckout($request, $token),
            'token' => $token,
        ]);
    }

    public function confirm(Request $request, string $token, BookingCheckoutService $checkoutService): RedirectResponse
    {
        $checkout = $this->resolveCheckout($request, $token);

        $request->validate([
            'payment_method' => ['required', 'in:ghost_card,moonwire_transfer,crypt_vault'],
            'cardholder_name' => ['required', 'string', 'max:120'],
            'card_number' => ['required', 'digits_between:12,19'],
            'expiry_month' => ['required', 'digits:2'],
            'expiry_year' => ['required', 'digits:2'],
            'security_code' => ['required', 'digits_between:3,4'],
            'acknowledge_demo' => ['accepted'],
        ]);

        $booking = $checkoutService->createFromCheckout($request->user(), $checkout);
        $this->forgetCheckout($request, $token);

        return redirect(BookingSupport::detailRoute($booking))
            ->with('status', 'Demonstration payment approved. Your booking is confirmed.');
    }

    private function storeCheckoutAndRedirect(Request $request, array $checkout): RedirectResponse
    {
        $token = (string) Str::uuid();
        $checkouts = $request->session()->get(self::SESSION_KEY, []);
        $payload = array_merge($checkout, [
            'user_id' => $request->user()->id,
            'prepared_at' => now()->toIso8601String(),
            'return_url' => url()->previous(),
        ]);
        $payload['qr_reference'] = $this->buildQrReference($token);
        $payload['qr_payload'] = $this->buildQrPayload($payload);
        $checkouts[$token] = $payload;

        $request->session()->put(self::SESSION_KEY, $checkouts);

        return redirect()->route('checkout.show', $token);
    }

    private function resolveCheckout(Request $request, string $token): array
    {
        $checkout = $request->session()->get(self::SESSION_KEY.'.'.$token);

        abort_unless(
            is_array($checkout) && ($checkout['user_id'] ?? null) === $request->user()->id,
            404
        );

        return $checkout;
    }

    private function forgetCheckout(Request $request, string $token): void
    {
        $checkouts = $request->session()->get(self::SESSION_KEY, []);
        unset($checkouts[$token]);
        $request->session()->put(self::SESSION_KEY, $checkouts);
    }

    private function buildQrReference(string $token): string
    {
        return 'HB-'.strtoupper(substr(str_replace('-', '', $token), 0, 10));
    }

    private function buildQrPayload(array $checkout): string
    {
        $summary = $checkout['summary'];

        return implode("\n", [
            'HORROR BARK DEMO PAYMENT',
            'Reference: '.($checkout['qr_reference'] ?? 'HB-DEMO'),
            'Type: '.$summary['type_label'],
            'Item: '.$summary['title'],
            'Schedule: '.$summary['schedule_label'],
            'Quantity: '.$summary['quantity'],
            'Total: MVR '.number_format((float) $summary['total_price'], 2, '.', ''),
            'Status: Awaiting simulated payment confirmation',
        ]);
    }
}
