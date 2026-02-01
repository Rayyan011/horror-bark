<?php

namespace Tests\Feature;

use App\Models\Hotel;
use App\Models\HotelBooking;
use App\Models\Invoice;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HotelBookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_book_a_room(): void
    {
        $user = User::factory()->create();

        Storage::fake('local');
        $this->app->instance('dompdf.wrapper', new class {
            public function loadView(string $view, array $data = []): self
            {
                return $this;
            }

            public function output(): string
            {
                return 'pdf-content';
            }
        });

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
            'price' => 120.00,
            'status' => 'available',
            'max_occupancy' => 2,
        ]);

        $response = $this->actingAs($user)->post(route('bookings.hotels.store', $room), [
            'start_date' => now()->addDays(1)->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'quantity' => 2,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('hotel_bookings', [
            'user_id' => $user->id,
            'room_id' => $room->id,
            'quantity' => 2,
            'status' => 'confirmed',
        ]);

        $booking = HotelBooking::first();
        $this->assertNotNull($booking);
        $this->assertDatabaseHas('invoices', [
            'invoiceable_type' => HotelBooking::class,
            'invoiceable_id' => $booking->id,
            'user_id' => $user->id,
            'amount' => $booking->total_price,
            'status' => 'issued',
        ]);

        $invoice = Invoice::first();
        $this->assertNotNull($invoice);
        $this->assertNotNull($invoice->pdf_path);
        Storage::disk('local')->assertExists($invoice->pdf_path);
    }
}
