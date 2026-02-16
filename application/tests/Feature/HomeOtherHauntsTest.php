<?php

namespace Tests\Feature;

use App\Models\BeachEvent;
use App\Models\Game;
use App\Models\Island;
use App\Models\Ride;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeOtherHauntsTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_other_haunts_no_longer_uses_static_seed_cards(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertDontSeeText('The Crypt Cafe');
        $response->assertDontSeeText('Pale Spa');
        $response->assertDontSeeText('Phantom Opera');
        $response->assertDontSeeText('Curio Shop');
    }

    public function test_home_other_haunts_renders_balanced_dynamic_cards_with_eight_item_cap(): void
    {
        $owner = User::factory()->create();
        $island = $this->createIsland();

        for ($index = 1; $index <= 4; $index++) {
            $this->createRide($owner, $island, 'Ride ' . $index, 20 + $index);
            $this->createGame($owner, $island, 'Game ' . $index, 30 + $index);
            $this->createBeachEvent($owner, $island, 'Beach Event ' . $index, 40 + $index, now()->addDays($index));
        }

        $response = $this->get(route('home'));
        $content = $response->getContent();

        $response->assertOk();
        $response->assertSee('data-testid="other-haunts-carousel"', false);
        $response->assertSee('data-visible-desktop="4"', false);

        $response->assertSeeTextInOrder([
            'Ride 1',
            'Game 1',
            'Beach Event 1',
            'Ride 2',
            'Game 2',
            'Beach Event 2',
            'Ride 3',
            'Game 3',
        ]);

        $response->assertDontSee('search=Ride+4', false);
        $response->assertDontSee('search=Game+4', false);
        $response->assertDontSee('search=Beach+Event+3', false);

        $this->assertSame(8, substr_count($content, 'data-testid="other-haunt-card-'));
    }

    public function test_home_other_haunts_cards_use_expected_catalog_links(): void
    {
        $owner = User::factory()->create();
        $island = $this->createIsland();

        $ride = $this->createRide($owner, $island, 'Wraith Spinner', 65);
        $game = $this->createGame($owner, $island, 'Hex Arcade', 48);
        $event = $this->createBeachEvent($owner, $island, 'Moonlit Tide Show', 72, now()->addWeek());

        $response = $this->get(route('home'));
        $content = $response->getContent();

        $response->assertOk();
        $this->assertMatchesRegularExpression(
            '/themepark\\?section=rides(?:&|&amp;)search=Wraith(?:\\+|%20)Spinner/',
            $content
        );
        $this->assertMatchesRegularExpression(
            '/themepark\\?section=games(?:&|&amp;)search=Hex(?:\\+|%20)Arcade/',
            $content
        );
        $this->assertMatchesRegularExpression(
            '/beach-events\\?search=Moonlit(?:\\+|%20)Tide(?:\\+|%20)Show/',
            $content
        );
    }

    private function createIsland(): Island
    {
        return Island::create([
            'name' => 'Horror Island',
            'type' => 'Horror-Island',
            'description' => 'Main island',
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
        ]);
    }

    private function createRide(User $owner, Island $island, string $name, int $price): Ride
    {
        return Ride::create([
            'user_id' => $owner->id,
            'island_id' => $island->id,
            'name' => $name,
            'price' => $price,
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
            'max_capacity' => 50,
            'max_booking_quantity' => 5,
        ]);
    }

    private function createGame(User $owner, Island $island, string $name, int $price): Game
    {
        return Game::create([
            'user_id' => $owner->id,
            'island_id' => $island->id,
            'name' => $name,
            'price' => $price,
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
            'max_capacity' => 50,
            'max_booking_quantity' => 5,
        ]);
    }

    private function createBeachEvent(User $owner, Island $island, string $name, int $price, \DateTimeInterface $eventDate): BeachEvent
    {
        return BeachEvent::create([
            'user_id' => $owner->id,
            'island_id' => $island->id,
            'name' => $name,
            'event_date' => $eventDate->format('Y-m-d'),
            'price' => $price,
            'max_capacity' => 120,
            'max_booking_quantity' => 6,
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
        ]);
    }
}
