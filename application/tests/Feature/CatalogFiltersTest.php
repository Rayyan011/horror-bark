<?php

namespace Tests\Feature;

use App\Models\Ferry;
use App\Models\Game;
use App\Models\Hotel;
use App\Models\Island;
use App\Models\Ride;
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
                'name' => 'Horror Ferry '.$index,
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
        $response->assertSee('value="'.$horrorIsland->id.'" selected', false);
        $response->assertSee('name="min_price"', false);
        $response->assertSee('name="max_price"', false);
        $response->assertSee('name="min_capacity"', false);
        $response->assertSee('type="range"', false);
    }

    public function test_themepark_combines_rides_and_games_and_uses_section_filter(): void
    {
        $owner = User::factory()->create();
        $island = Island::create([
            'name' => 'Harbor Ward',
            'type' => 'Horror-Island',
            'description' => 'Harbor',
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
        ]);

        Ride::create([
            'user_id' => $owner->id,
            'island_id' => $island->id,
            'name' => 'Nocturne Drop',
            'price' => 210,
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
            'max_capacity' => 20,
            'max_booking_quantity' => 4,
        ]);

        Game::create([
            'user_id' => $owner->id,
            'island_id' => $island->id,
            'name' => 'Midnight Draw',
            'price' => 55,
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
            'max_capacity' => 18,
            'max_booking_quantity' => 3,
        ]);

        $response = $this->get(route('themepark.index'));

        $response->assertOk();
        $response->assertSee('Nocturne Drop');
        $response->assertSee('Midnight Draw');
        $response->assertSee('Active Attractions');
        $response->assertSee('name="min_price"', false);
        $response->assertSee('name="min_capacity"', false);
        $response->assertSee('type="range"', false);

        $gamesOnly = $this->get(route('themepark.index', [
            'section' => 'games',
        ]));

        $gamesOnly->assertOk();
        $gamesOnly->assertDontSee('Nocturne Drop');
        $gamesOnly->assertSee('Midnight Draw');
    }
}
