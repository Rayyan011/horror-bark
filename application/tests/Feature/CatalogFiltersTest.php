<?php

namespace Tests\Feature;

use App\Models\Ferry;
use App\Models\Hotel;
use App\Models\Island;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogFiltersTest extends TestCase
{
    use RefreshDatabase;

    public function test_hotel_filters_apply_search_and_price_range(): void
    {
        $hotelOwner = User::factory()->create();

        $nightHotel = Hotel::create([
            'user_id' => $hotelOwner->id,
            'name' => 'Nightfall Inn',
            'location' => 'Harbor',
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
        ]);

        Room::create([
            'hotel_id' => $nightHotel->id,
            'room_number' => '101',
            'price' => 200,
            'status' => 'available',
            'max_occupancy' => 2,
            'images' => [],
        ]);

        $budgetHotel = Hotel::create([
            'user_id' => $hotelOwner->id,
            'name' => 'Budget Stay',
            'location' => 'Village',
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
        ]);

        Room::create([
            'hotel_id' => $budgetHotel->id,
            'room_number' => '102',
            'price' => 60,
            'status' => 'available',
            'max_occupancy' => 2,
            'images' => [],
        ]);

        $response = $this->get(route('hotels.index', [
            'search' => 'Night',
            'min_price' => 150,
        ]));

        $response->assertOk();
        $response->assertSee('Nightfall Inn');
        $response->assertDontSee('Budget Stay');
    }

    public function test_ferry_filters_apply_island_and_price_and_preserve_query_string(): void
    {
        $owner = User::factory()->create();
        $horrorIsland = Island::create([
            'name' => 'Horror Island',
            'type' => 'Horror-Island',
            'description' => 'Horror',
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
        ]);
        $picnicIsland = Island::create([
            'name' => 'Picnic Island',
            'type' => 'Picnic-Island',
            'description' => 'Picnic',
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
        ]);

        foreach (range(1, 13) as $index) {
            Ferry::create([
                'user_id' => $owner->id,
                'name' => 'Horror Ferry ' . $index,
                'price' => 50 + $index,
                'max_capacity' => 100,
                'max_booking_quantity' => 5,
                'island_id' => $horrorIsland->id,
            ]);
        }

        Ferry::create([
            'user_id' => $owner->id,
            'name' => 'Picnic Ferry',
            'price' => 40,
            'max_capacity' => 80,
            'max_booking_quantity' => 5,
            'island_id' => $picnicIsland->id,
        ]);

        $response = $this->get(route('ferries.index', [
            'island_id' => $horrorIsland->id,
            'min_price' => 55,
            'sort' => 'name_asc',
        ]));

        $response->assertOk();
        $response->assertDontSee('Picnic Ferry');
        $response->assertSee('value="' . $horrorIsland->id . '" selected', false);
        $response->assertSee('name="min_price" type="number" min="0" step="0.01" value="55"', false);
    }
}
