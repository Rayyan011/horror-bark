<?php

namespace Tests\Feature;

use App\Models\BeachEvent;
use App\Models\Ferry;
use App\Models\Game;
use App\Models\Hotel;
use App\Models\HotelBooking;
use App\Models\Island;
use App\Models\Ride;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class IslandAccessBookingRulesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        $this->app->instance('dompdf.wrapper', new class
        {
            public function loadView(string $view, array $data = []): self
            {
                return $this;
            }

            public function output(): string
            {
                return 'pdf-content';
            }
        });
    }

    public function test_ferry_to_horror_island_requires_hotel_booking(): void
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();
        $horrorIsland = $this->createIsland('Horror', 'Horror-Island');

        $ferry = Ferry::create([
            'user_id' => $owner->id,
            'name' => 'Horror Transfer',
            'price' => 40,
            'max_capacity' => 100,
            'max_booking_quantity' => 5,
            'island_id' => $horrorIsland->id,
        ]);

        $response = $this->actingAs($user)->post(route('bookings.ferries.store', $ferry), [
            'booking_time' => now()->addDay()->setTime(10, 0)->format('Y-m-d H:i:s'),
            'quantity' => 2,
        ]);

        $response->assertSessionHasErrors('booking_time');
        $this->assertDatabaseCount('ferry_bookings', 0);
    }

    public function test_ferry_to_picnic_island_does_not_require_hotel_booking(): void
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();
        $picnicIsland = $this->createIsland('Picnic', 'Picnic-Island');

        $ferry = Ferry::create([
            'user_id' => $owner->id,
            'name' => 'Picnic Shuttle',
            'price' => 30,
            'max_capacity' => 100,
            'max_booking_quantity' => 5,
            'island_id' => $picnicIsland->id,
        ]);

        $response = $this->actingAs($user)->post(route('bookings.ferries.store', $ferry), [
            'booking_time' => now()->addDay()->setTime(10, 0)->format('Y-m-d H:i:s'),
            'quantity' => 1,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('ferry_bookings', [
            'user_id' => $user->id,
            'ferry_id' => $ferry->id,
            'status' => 'confirmed',
        ]);
    }

    public function test_ride_booking_requires_hotel_booking(): void
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();
        $horrorIsland = $this->createIsland('Horror', 'Horror-Island');

        $ride = Ride::create([
            'user_id' => $owner->id,
            'island_id' => $horrorIsland->id,
            'name' => 'Ghost Coaster',
            'price' => 25,
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
            'max_capacity' => 80,
            'max_booking_quantity' => 4,
        ]);

        $response = $this->actingAs($user)->post(route('bookings.rides.store', $ride), [
            'booking_time' => now()->addDay()->setTime(9, 0)->format('Y-m-d H:i:s'),
            'quantity' => 1,
        ]);

        $response->assertSessionHasErrors('booking_time');
        $this->assertDatabaseCount('ride_bookings', 0);
    }

    public function test_ride_booking_requires_hotel_booking_even_on_picnic_island(): void
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();
        $picnicIsland = $this->createIsland('Picnic', 'Picnic-Island');

        $ride = Ride::create([
            'user_id' => $owner->id,
            'island_id' => $picnicIsland->id,
            'name' => 'Picnic Drop',
            'price' => 25,
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
            'max_capacity' => 80,
            'max_booking_quantity' => 4,
        ]);

        $response = $this->actingAs($user)->post(route('bookings.rides.store', $ride), [
            'booking_time' => now()->addDay()->setTime(9, 0)->format('Y-m-d H:i:s'),
            'quantity' => 1,
        ]);

        $response->assertSessionHasErrors('booking_time');
        $this->assertDatabaseCount('ride_bookings', 0);
    }

    public function test_game_booking_requires_hotel_booking(): void
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();
        $horrorIsland = $this->createIsland('Horror', 'Horror-Island');

        $game = Game::create([
            'user_id' => $owner->id,
            'island_id' => $horrorIsland->id,
            'name' => 'Haunted Arcade',
            'price' => 20,
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
            'max_capacity' => 60,
            'max_booking_quantity' => 3,
        ]);

        $response = $this->actingAs($user)->post(route('bookings.games.store', $game), [
            'booking_time' => now()->addDay()->setTime(9, 0)->format('Y-m-d H:i:s'),
            'quantity' => 1,
        ]);

        $response->assertSessionHasErrors('booking_time');
        $this->assertDatabaseCount('game_bookings', 0);
    }

    public function test_game_booking_requires_hotel_booking_even_on_picnic_island(): void
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();
        $picnicIsland = $this->createIsland('Picnic', 'Picnic-Island');

        $game = Game::create([
            'user_id' => $owner->id,
            'island_id' => $picnicIsland->id,
            'name' => 'Picnic Puzzle',
            'price' => 20,
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
            'max_capacity' => 60,
            'max_booking_quantity' => 3,
        ]);

        $response = $this->actingAs($user)->post(route('bookings.games.store', $game), [
            'booking_time' => now()->addDay()->setTime(9, 0)->format('Y-m-d H:i:s'),
            'quantity' => 1,
        ]);

        $response->assertSessionHasErrors('booking_time');
        $this->assertDatabaseCount('game_bookings', 0);
    }

    public function test_beach_event_on_picnic_island_requires_hotel_booking(): void
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();
        $picnicIsland = $this->createIsland('Picnic', 'Picnic-Island');

        $eventDate = now()->addDays(2)->toDateString();
        $event = BeachEvent::create([
            'user_id' => $owner->id,
            'island_id' => $picnicIsland->id,
            'name' => 'Moonlight Concert',
            'event_date' => $eventDate,
            'price' => 50,
            'max_capacity' => 100,
            'max_booking_quantity' => 5,
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
        ]);

        $response = $this->actingAs($user)->post(route('bookings.beach-events.store', $event), [
            'booking_date' => $eventDate,
            'booking_time' => '12:00',
            'quantity' => 2,
        ]);

        $response->assertSessionHasErrors('booking_time');
        $this->assertDatabaseCount('beach_event_bookings', 0);
    }

    public function test_beach_event_allows_overlapping_confirmed_hotel_stay(): void
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();
        $picnicIsland = $this->createIsland('Picnic', 'Picnic-Island');

        $eventDate = now()->addDays(2)->toDateString();
        $event = BeachEvent::create([
            'user_id' => $owner->id,
            'island_id' => $picnicIsland->id,
            'name' => 'Moonlight Concert',
            'event_date' => $eventDate,
            'price' => 50,
            'max_capacity' => 100,
            'max_booking_quantity' => 5,
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
        ]);
        $bookingAt = Carbon::parse($eventDate)->setTime(12, 0);

        $this->createHotelStay(
            $user,
            $bookingAt->copy()->subDay(),
            $bookingAt->copy()->addDay(),
            'confirmed',
        );

        $response = $this->actingAs($user)->post(route('bookings.beach-events.store', $event), [
            'booking_date' => $eventDate,
            'booking_time' => '12:00',
            'quantity' => 2,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('beach_event_bookings', [
            'user_id' => $user->id,
            'beach_event_id' => $event->id,
            'status' => 'confirmed',
        ]);
    }

    public function test_horror_access_allows_only_overlapping_confirmed_hotel_stay(): void
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();
        $horrorIsland = $this->createIsland('Horror', 'Horror-Island');

        $ferry = Ferry::create([
            'user_id' => $owner->id,
            'name' => 'Night Ferry',
            'price' => 40,
            'max_capacity' => 100,
            'max_booking_quantity' => 5,
            'island_id' => $horrorIsland->id,
        ]);

        $bookingAt = now()->addDays(3)->setTime(10, 0);

        $this->createHotelStay(
            $user,
            $bookingAt->copy()->subDay(),
            $bookingAt->copy()->addDay(),
            'confirmed'
        );

        $success = $this->actingAs($user)->post(route('bookings.ferries.store', $ferry), [
            'booking_time' => $bookingAt->format('Y-m-d H:i:s'),
            'quantity' => 1,
        ]);

        $success->assertSessionHasNoErrors();
        $this->assertDatabaseHas('ferry_bookings', [
            'user_id' => $user->id,
            'ferry_id' => $ferry->id,
            'status' => 'confirmed',
        ]);

        $userWithoutOverlap = User::factory()->create();
        $this->createHotelStay(
            $userWithoutOverlap,
            $bookingAt->copy()->subDays(5),
            $bookingAt->copy()->subDays(3),
            'confirmed'
        );

        $blocked = $this->actingAs($userWithoutOverlap)->post(route('bookings.ferries.store', $ferry), [
            'booking_time' => $bookingAt->format('Y-m-d H:i:s'),
            'quantity' => 1,
        ]);

        $blocked->assertSessionHasErrors('booking_time');
    }

    public function test_horror_island_ferry_allows_checkout_day_departure(): void
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();
        $horrorIsland = $this->createIsland('Horror', 'Horror-Island');

        $ferry = Ferry::create([
            'user_id' => $owner->id,
            'name' => 'Checkout Ferry',
            'price' => 40,
            'max_capacity' => 100,
            'max_booking_quantity' => 5,
            'island_id' => $horrorIsland->id,
        ]);

        $checkoutDay = now()->addDays(5)->startOfDay();
        $bookingAt = $checkoutDay->copy()->setTime(16, 0);

        $this->createHotelStay(
            $user,
            $checkoutDay->copy()->subDays(2),
            $checkoutDay,
            'confirmed',
        );

        $response = $this->actingAs($user)->post(route('bookings.ferries.store', $ferry), [
            'booking_time' => $bookingAt->format('Y-m-d H:i:s'),
            'quantity' => 1,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('ferry_bookings', [
            'user_id' => $user->id,
            'ferry_id' => $ferry->id,
            'status' => 'confirmed',
        ]);
    }

    public function test_ride_booking_still_excludes_checkout_day(): void
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();
        $horrorIsland = $this->createIsland('Horror', 'Horror-Island');

        $ride = Ride::create([
            'user_id' => $owner->id,
            'island_id' => $horrorIsland->id,
            'name' => 'Checkout Coaster',
            'price' => 25,
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
            'max_capacity' => 80,
            'max_booking_quantity' => 4,
        ]);

        $checkoutDay = now()->addDays(5)->startOfDay();
        $bookingAt = $checkoutDay->copy()->setTime(9, 0);

        $this->createHotelStay(
            $user,
            $checkoutDay->copy()->subDays(2),
            $checkoutDay,
            'confirmed',
        );

        $response = $this->actingAs($user)->post(route('bookings.rides.store', $ride), [
            'booking_time' => $bookingAt->format('Y-m-d H:i:s'),
            'quantity' => 1,
        ]);

        $response->assertSessionHasErrors('booking_time');
        $this->assertDatabaseCount('ride_bookings', 0);
    }

    private function createIsland(string $name, string $type): Island
    {
        return Island::create([
            'name' => $name.' Island',
            'type' => $type,
            'description' => $name.' description',
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
        ]);
    }

    private function createHotelStay(User $user, Carbon $start, Carbon $end, string $status): HotelBooking
    {
        $hotelOwner = User::factory()->create();
        $hotel = Hotel::create([
            'user_id' => $hotelOwner->id,
            'name' => 'Horror Hotel',
            'location' => 'Main Island',
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
        ]);

        $room = Room::create([
            'hotel_id' => $hotel->id,
            'room_number' => '101',
            'price' => 120.00,
            'status' => 'available',
            'max_occupancy' => 4,
            'images' => [],
        ]);

        return HotelBooking::create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'start_date' => $start,
            'end_date' => $end,
            'quantity' => 2,
            'total_price' => 240.00,
            'status' => $status,
        ]);
    }
}
