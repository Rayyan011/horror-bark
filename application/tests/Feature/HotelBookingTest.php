<?php

namespace Tests\Feature;

use App\Models\Hotel;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HotelBookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_book_a_room(): void
    {
        $user = User::factory()->create();

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
            'status' => 'pending',
        ]);
    }
}
