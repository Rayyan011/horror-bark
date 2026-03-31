<?php

namespace Tests\Feature;

use App\Filament\Ferry\Resources\FerryBookingResource;
use App\Filament\Ferry\Resources\FerryResource;
use App\Filament\Game\Resources\GameBookingResource;
use App\Filament\Game\Resources\GameResource;
use App\Filament\Ride\Resources\RideBookingResource;
use App\Models\Ferry;
use App\Models\FerryBooking;
use App\Models\Game;
use App\Models\GameBooking;
use App\Models\Island;
use App\Models\Ride;
use App\Models\RideBooking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OperatorPanelScopingTest extends TestCase
{
    use RefreshDatabase;

    public function test_ferry_operator_resource_query_only_returns_owned_ferries(): void
    {
        $operator = User::factory()->create();
        $otherOperator = User::factory()->create();
        $island = $this->createIsland();

        $ownedFerry = Ferry::create([
            'user_id' => $operator->id,
            'name' => 'Owned Ferry',
            'price' => 50,
            'max_capacity' => 100,
            'max_booking_quantity' => 5,
            'island_id' => $island->id,
        ]);

        Ferry::create([
            'user_id' => $otherOperator->id,
            'name' => 'Other Ferry',
            'price' => 60,
            'max_capacity' => 100,
            'max_booking_quantity' => 5,
            'island_id' => $island->id,
        ]);

        $this->actingAs($operator);

        $visibleIds = FerryResource::getEloquentQuery()->pluck('id')->all();

        $this->assertSame([$ownedFerry->id], $visibleIds);
    }

    public function test_ferry_operator_booking_query_only_returns_bookings_for_owned_ferries(): void
    {
        $operator = User::factory()->create();
        $otherOperator = User::factory()->create();
        $customer = User::factory()->create();
        $island = $this->createIsland();

        $ownedFerry = Ferry::create([
            'user_id' => $operator->id,
            'name' => 'Owned Ferry',
            'price' => 50,
            'max_capacity' => 100,
            'max_booking_quantity' => 5,
            'island_id' => $island->id,
        ]);

        $otherFerry = Ferry::create([
            'user_id' => $otherOperator->id,
            'name' => 'Other Ferry',
            'price' => 60,
            'max_capacity' => 100,
            'max_booking_quantity' => 5,
            'island_id' => $island->id,
        ]);

        $ownedBooking = FerryBooking::create([
            'user_id' => $customer->id,
            'ferry_id' => $ownedFerry->id,
            'booking_time' => now()->addDay()->setTime(10, 0),
            'quantity' => 2,
            'total_price' => 100,
            'status' => 'confirmed',
        ]);

        FerryBooking::create([
            'user_id' => $customer->id,
            'ferry_id' => $otherFerry->id,
            'booking_time' => now()->addDay()->setTime(11, 0),
            'quantity' => 1,
            'total_price' => 60,
            'status' => 'confirmed',
        ]);

        $this->actingAs($operator);

        $visibleIds = FerryBookingResource::getEloquentQuery()->pluck('id')->all();

        $this->assertSame([$ownedBooking->id], $visibleIds);
    }

    public function test_game_operator_resource_query_only_returns_owned_games(): void
    {
        $operator = User::factory()->create();
        $otherOperator = User::factory()->create();
        $island = $this->createIsland();

        $ownedGame = Game::create([
            'user_id' => $operator->id,
            'island_id' => $island->id,
            'name' => 'Owned Game',
            'price' => 30,
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
            'max_capacity' => 40,
            'max_booking_quantity' => 4,
        ]);

        Game::create([
            'user_id' => $otherOperator->id,
            'island_id' => $island->id,
            'name' => 'Other Game',
            'price' => 35,
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
            'max_capacity' => 40,
            'max_booking_quantity' => 4,
        ]);

        $this->actingAs($operator);

        $visibleIds = GameResource::getEloquentQuery()->pluck('id')->all();

        $this->assertSame([$ownedGame->id], $visibleIds);
    }

    public function test_game_operator_booking_query_only_returns_bookings_for_owned_games(): void
    {
        $operator = User::factory()->create();
        $otherOperator = User::factory()->create();
        $customer = User::factory()->create();
        $island = $this->createIsland();

        $ownedGame = Game::create([
            'user_id' => $operator->id,
            'island_id' => $island->id,
            'name' => 'Owned Game',
            'price' => 30,
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
            'max_capacity' => 40,
            'max_booking_quantity' => 4,
        ]);

        $otherGame = Game::create([
            'user_id' => $otherOperator->id,
            'island_id' => $island->id,
            'name' => 'Other Game',
            'price' => 35,
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
            'max_capacity' => 40,
            'max_booking_quantity' => 4,
        ]);

        $ownedBooking = GameBooking::create([
            'user_id' => $customer->id,
            'game_id' => $ownedGame->id,
            'booking_time' => now()->addDay()->setTime(9, 0),
            'quantity' => 2,
            'total_price' => 60,
            'status' => 'confirmed',
        ]);

        GameBooking::create([
            'user_id' => $customer->id,
            'game_id' => $otherGame->id,
            'booking_time' => now()->addDay()->setTime(17, 0),
            'quantity' => 1,
            'total_price' => 35,
            'status' => 'confirmed',
        ]);

        $this->actingAs($operator);

        $visibleIds = GameBookingResource::getEloquentQuery()->pluck('id')->all();

        $this->assertSame([$ownedBooking->id], $visibleIds);
    }

    public function test_ride_operator_booking_query_only_returns_bookings_for_owned_rides(): void
    {
        $operator = User::factory()->create();
        $otherOperator = User::factory()->create();
        $customer = User::factory()->create();
        $island = $this->createIsland();

        $ownedRide = Ride::create([
            'user_id' => $operator->id,
            'island_id' => $island->id,
            'name' => 'Owned Ride',
            'price' => 45,
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
            'max_capacity' => 60,
            'max_booking_quantity' => 4,
        ]);

        $otherRide = Ride::create([
            'user_id' => $otherOperator->id,
            'island_id' => $island->id,
            'name' => 'Other Ride',
            'price' => 55,
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
            'max_capacity' => 60,
            'max_booking_quantity' => 4,
        ]);

        $ownedBooking = RideBooking::create([
            'user_id' => $customer->id,
            'ride_id' => $ownedRide->id,
            'booking_time' => now()->addDay()->setTime(9, 0),
            'quantity' => 1,
            'total_price' => 45,
            'status' => 'confirmed',
        ]);

        RideBooking::create([
            'user_id' => $customer->id,
            'ride_id' => $otherRide->id,
            'booking_time' => now()->addDay()->setTime(17, 0),
            'quantity' => 1,
            'total_price' => 55,
            'status' => 'confirmed',
        ]);

        $this->actingAs($operator);

        $visibleIds = RideBookingResource::getEloquentQuery()->pluck('id')->all();

        $this->assertSame([$ownedBooking->id], $visibleIds);
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
}
