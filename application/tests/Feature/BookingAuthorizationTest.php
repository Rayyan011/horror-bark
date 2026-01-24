<?php

namespace Tests\Feature;

use App\Models\Ferry;
use App\Models\FerryBooking;
use App\Models\Island;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_cannot_cancel_someone_elses_booking(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $island = Island::create([
            'name' => 'Skull Island',
            'type' => 'theme_park',
            'description' => 'Haunted shores',
            'latitude' => 0,
            'longitude' => 0,
            'images' => [],
        ]);

        $ferry = Ferry::create([
            'user_id' => $owner->id,
            'name' => 'Night Ferry',
            'price' => 25.00,
            'max_capacity' => 50,
            'max_booking_quantity' => 5,
            'island_id' => $island->id,
        ]);

        $booking = FerryBooking::create([
            'user_id' => $owner->id,
            'ferry_id' => $ferry->id,
            'booking_time' => now()->addDays(1)->setTime(9, 0),
            'quantity' => 2,
            'total_price' => 50.00,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($otherUser)->patch(route('bookings.ferries.cancel', $booking));

        $response->assertStatus(403);
    }
}
