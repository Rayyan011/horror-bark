<?php

namespace Tests\Feature;

use App\Models\Hotel;
use App\Models\HotelBooking;
use App\Models\Invoice;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceDisplayTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_page_hides_raw_booking_json_and_uses_invoice_id(): void
    {
        [$user, $invoice] = $this->createInvoice();

        $response = $this->actingAs($user)->get(route('invoices.show', $invoice));

        $response->assertOk();
        $response->assertSee('Invoice ID');
        $response->assertSee('#'.$invoice->id);
        $response->assertSee('Booking Type');
        $response->assertDontSee('Booking ID');
        $response->assertDontSee('room_id');
        $response->assertDontSee('invoiceable_id');
    }

    public function test_invoice_pdf_uses_invoice_id_instead_of_booking_id(): void
    {
        [, $invoice] = $this->createInvoice();

        $html = view('invoices.pdf', [
            'invoice' => $invoice->load('invoiceable', 'user'),
        ])->render();

        $this->assertStringContainsString('Invoice ID', $html);
        $this->assertStringContainsString((string) $invoice->id, $html);
        $this->assertStringNotContainsString('Booking ID', $html);
    }

    private function createInvoice(): array
    {
        $user = User::factory()->create([
            'name' => 'Test Guest',
        ]);

        $hotel = Hotel::create([
            'name' => 'Nightfall Inn',
            'location' => 'Harbor',
            'latitude' => 0,
            'longitude' => 0,
            'images' => [],
        ]);

        $room = Room::create([
            'hotel_id' => $hotel->id,
            'room_number' => '101',
            'price' => 120,
            'status' => 'available',
            'max_occupancy' => 2,
            'images' => [],
        ]);

        $booking = HotelBooking::create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'start_date' => now()->addDay(),
            'end_date' => now()->addDays(2),
            'quantity' => 1,
            'total_price' => 120,
            'status' => 'confirmed',
        ]);

        $invoice = Invoice::create([
            'invoice_number' => 'INV-TEST-0001',
            'invoiceable_type' => HotelBooking::class,
            'invoiceable_id' => $booking->id,
            'user_id' => $user->id,
            'amount' => 120,
            'status' => 'issued',
            'issued_at' => now(),
            'pdf_path' => 'invoices/inv-test-0001.pdf',
        ]);

        return [$user, $invoice];
    }
}
