<?php

namespace Tests\Feature;

use App\Models\Hotel;
use App\Models\HotelBooking;
use App\Models\Invoice;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalAccordionAndReceiptsTest extends TestCase
{
    use RefreshDatabase;

    public function test_portal_groups_bookings_by_status_and_shows_only_user_receipts(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $owner = User::factory()->create();

        $hotel = Hotel::create([
            'user_id' => $owner->id,
            'name' => 'Horror Hotel',
            'location' => 'Main Island',
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
        ]);

        $room = Room::create([
            'hotel_id' => $hotel->id,
            'room_number' => '101',
            'price' => 120,
            'status' => 'available',
            'max_occupancy' => 3,
            'images' => [],
        ]);

        $pendingBooking = HotelBooking::create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'start_date' => now()->addDays(1),
            'end_date' => now()->addDays(2),
            'quantity' => 1,
            'total_price' => 120,
            'status' => 'pending',
        ]);

        $confirmedBooking = HotelBooking::create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'start_date' => now()->addDays(3),
            'end_date' => now()->addDays(4),
            'quantity' => 1,
            'total_price' => 120,
            'status' => 'confirmed',
        ]);

        $canceledBooking = HotelBooking::create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'start_date' => now()->addDays(5),
            'end_date' => now()->addDays(6),
            'quantity' => 1,
            'total_price' => 120,
            'status' => 'canceled',
        ]);

        Invoice::create([
            'invoice_number' => 'INV-USER-0001',
            'invoiceable_type' => HotelBooking::class,
            'invoiceable_id' => $confirmedBooking->id,
            'user_id' => $user->id,
            'amount' => 120,
            'status' => 'issued',
            'issued_at' => now(),
            'pdf_path' => 'invoices/inv-user-0001.pdf',
        ]);

        $otherBooking = HotelBooking::create([
            'user_id' => $otherUser->id,
            'room_id' => $room->id,
            'start_date' => now()->addDays(7),
            'end_date' => now()->addDays(8),
            'quantity' => 1,
            'total_price' => 120,
            'status' => 'confirmed',
        ]);

        Invoice::create([
            'invoice_number' => 'INV-OTHER-0001',
            'invoiceable_type' => HotelBooking::class,
            'invoiceable_id' => $otherBooking->id,
            'user_id' => $otherUser->id,
            'amount' => 120,
            'status' => 'issued',
            'issued_at' => now(),
            'pdf_path' => 'invoices/inv-other-0001.pdf',
        ]);

        $response = $this->actingAs($user)->get(route('bookings.index'));

        $response->assertOk();
        $response->assertSee('Pending');
        $response->assertSee('Confirmed');
        $response->assertSee('Canceled');
        $response->assertSee('INV-USER-0001');
        $response->assertDontSee('INV-OTHER-0001');
        $response->assertSee((string) $pendingBooking->quantity);
        $response->assertSee((string) $confirmedBooking->quantity);
        $response->assertSee((string) $canceledBooking->quantity);
    }
}
