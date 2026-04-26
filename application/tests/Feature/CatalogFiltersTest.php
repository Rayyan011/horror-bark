<?php

namespace Tests\Feature;

use App\Models\BeachEvent;
use App\Models\Ferry;
use App\Models\Game;
use App\Models\Hotel;
use App\Models\Island;
use App\Models\Ride;
use App\Models\Room;
use App\Models\User;
use App\Services\IslandAccessService;
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

        $typeFiltered = $this->get(route('ferries.index', [
            'island_type' => IslandAccessService::PICNIC_ISLAND,
        ]));

        $typeFiltered->assertOk();
        $typeFiltered->assertSee('Picnic Ferry');
        $typeFiltered->assertDontSee('Horror Ferry 1');
        $typeFiltered->assertSee('value="'.IslandAccessService::PICNIC_ISLAND.'" selected', false);
    }

    public function test_ferry_booking_form_uses_whole_hour_steps_and_scoped_validation_errors(): void
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();
        $horrorIsland = Island::create([
            'name' => 'Horror Island',
            'type' => IslandAccessService::HORROR_ISLAND,
            'description' => 'Horror',
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
        ]);
        $picnicIsland = Island::create([
            'name' => 'Picnic Island',
            'type' => IslandAccessService::PICNIC_ISLAND,
            'description' => 'Picnic',
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
        ]);

        $horrorFerry = Ferry::create([
            'user_id' => $owner->id,
            'name' => 'Keeper Passage',
            'price' => 75,
            'max_capacity' => 80,
            'max_booking_quantity' => 5,
            'island_id' => $horrorIsland->id,
        ]);

        $picnicFerry = Ferry::create([
            'user_id' => $owner->id,
            'name' => 'Moonwake Line',
            'price' => 60,
            'max_capacity' => 80,
            'max_booking_quantity' => 5,
            'island_id' => $picnicIsland->id,
        ]);

        $listing = $this->actingAs($user)->get(route('ferries.index'));

        $listing->assertOk();
        $listing->assertSee('step="3600"', false);
        $listing->assertSee('name="_booking_form_id" value="ferry_'.$picnicFerry->id.'"', false);

        $invalidTime = now()->addDay()->setTime(9, 15)->format('Y-m-d\TH:i');

        $this->actingAs($user)
            ->from(route('ferries.index'))
            ->post(route('checkout.ferries.prepare', $picnicFerry), [
                '_booking_form_id' => 'ferry_'.$picnicFerry->id,
                'booking_time' => $invalidTime,
                'quantity' => 1,
            ])
            ->assertRedirect(route('ferries.index'));

        $response = $this->actingAs($user)->get(route('ferries.index'));
        $content = $response->getContent();

        $response->assertOk();
        $this->assertSame(1, substr_count($content, 'border-rose-500'));
        $this->assertStringContainsString('id="ferry_'.$picnicFerry->id.'_datetime"', $content);
        $this->assertStringContainsString('value="'.$invalidTime.'"', $content);
        $this->assertStringContainsString('id="ferry_'.$horrorFerry->id.'_datetime"', $content);
        $this->assertStringNotContainsString('id="ferry_'.$horrorFerry->id.'_datetime" name="booking_time" type="datetime-local" step="3600" value="'.$invalidTime.'"', $content);
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

    public function test_themepark_filters_by_island_type(): void
    {
        $owner = User::factory()->create();
        $horrorIsland = Island::create([
            'name' => 'Manor Ward',
            'type' => IslandAccessService::HORROR_ISLAND,
            'description' => 'Horror',
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
        ]);
        $picnicIsland = Island::create([
            'name' => 'Picnic Strand',
            'type' => IslandAccessService::PICNIC_ISLAND,
            'description' => 'Picnic',
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
        ]);

        Ride::create([
            'user_id' => $owner->id,
            'island_id' => $horrorIsland->id,
            'name' => 'Manor Drop',
            'price' => 190,
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
            'max_capacity' => 20,
            'max_booking_quantity' => 4,
        ]);

        Game::create([
            'user_id' => $owner->id,
            'island_id' => $picnicIsland->id,
            'name' => 'Strand Puzzle',
            'price' => 45,
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
            'max_capacity' => 18,
            'max_booking_quantity' => 3,
        ]);

        $horror = $this->get(route('themepark.index', [
            'island_type' => IslandAccessService::HORROR_ISLAND,
        ]));

        $horror->assertOk();
        $horror->assertSee('Manor Drop');
        $horror->assertDontSee('Strand Puzzle');
        $horror->assertSee('value="'.IslandAccessService::HORROR_ISLAND.'" selected', false);

        $picnic = $this->get(route('themepark.index', [
            'island_type' => IslandAccessService::PICNIC_ISLAND,
        ]));

        $picnic->assertOk();
        $picnic->assertDontSee('Manor Drop');
        $picnic->assertSee('Strand Puzzle');
        $picnic->assertSee('value="'.IslandAccessService::PICNIC_ISLAND.'" selected', false);
    }

    public function test_beach_events_filter_by_island_type(): void
    {
        $owner = User::factory()->create();
        $horrorIsland = Island::create([
            'name' => 'Manor Ward',
            'type' => IslandAccessService::HORROR_ISLAND,
            'description' => 'Horror',
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
        ]);
        $picnicIsland = Island::create([
            'name' => 'Picnic Strand',
            'type' => IslandAccessService::PICNIC_ISLAND,
            'description' => 'Picnic',
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
        ]);

        BeachEvent::create([
            'user_id' => $owner->id,
            'island_id' => $horrorIsland->id,
            'name' => 'Manor Masquerade',
            'event_date' => now()->addDays(4)->toDateString(),
            'price' => 90,
            'max_capacity' => 80,
            'max_booking_quantity' => 4,
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
        ]);

        BeachEvent::create([
            'user_id' => $owner->id,
            'island_id' => $picnicIsland->id,
            'name' => 'Moonlit Picnic',
            'event_date' => now()->addDays(5)->toDateString(),
            'price' => 70,
            'max_capacity' => 80,
            'max_booking_quantity' => 4,
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
        ]);

        $response = $this->get(route('beach-events.index', [
            'island_type' => IslandAccessService::PICNIC_ISLAND,
        ]));

        $response->assertOk();
        $response->assertDontSee('Manor Masquerade');
        $response->assertSee('Moonlit Picnic');
        $response->assertSee('value="'.IslandAccessService::PICNIC_ISLAND.'" selected', false);
    }
}
