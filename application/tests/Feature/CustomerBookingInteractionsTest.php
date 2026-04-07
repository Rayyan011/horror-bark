<?php

namespace Tests\Feature;

use App\Models\BeachEvent;
use App\Models\BeachEventBooking;
use App\Models\Ferry;
use App\Models\FerryBooking;
use App\Models\Game;
use App\Models\GameBooking;
use App\Models\Hotel;
use App\Models\HotelBooking;
use App\Models\Island;
use App\Models\Ride;
use App\Models\RideBooking;
use App\Models\Room;
use App\Models\User;
use App\Services\IslandAccessService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CustomerBookingInteractionsTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;

    protected User $owner;

    protected Island $horrorIsland;

    protected Island $picnicIsland;

    protected function setUp(): void
    {
        parent::setUp();

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

        $this->customer = User::factory()->create();
        $this->owner = User::factory()->create();

        $this->horrorIsland = Island::create([
            'name' => 'Manor Ward',
            'type' => IslandAccessService::HORROR_ISLAND,
            'description' => 'Horror district',
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
        ]);

        $this->picnicIsland = Island::create([
            'name' => 'Pale Moon Strand',
            'type' => IslandAccessService::PICNIC_ISLAND,
            'description' => 'Picnic district',
            'latitude' => 4.21,
            'longitude' => 73.41,
            'images' => [],
        ]);
    }

    public function test_customer_can_create_reschedule_and_cancel_a_hotel_booking(): void
    {
        $hotel = $this->createHotel('The Shining Manor');
        $room = Room::create([
            'hotel_id' => $hotel->id,
            'room_number' => 'SM-101',
            'price' => 780,
            'status' => 'available',
            'max_occupancy' => 2,
            'amenities' => ['Moonlit bath'],
            'images' => [],
            'description' => 'North tower suite',
        ]);

        $create = $this->actingAs($this->customer)->post(route('bookings.hotels.store', $room), [
            'start_date' => now()->addDays(6)->toDateString(),
            'end_date' => now()->addDays(8)->toDateString(),
            'quantity' => 2,
        ]);

        $create->assertSessionHasNoErrors();

        $booking = HotelBooking::query()->firstOrFail();

        $reschedule = $this->actingAs($this->customer)->patch(route('bookings.hotels.reschedule', $booking), [
            'start_date' => now()->addDays(9)->toDateString(),
            'end_date' => now()->addDays(11)->toDateString(),
        ]);

        $reschedule->assertSessionHasNoErrors();
        $this->assertSame(now()->addDays(9)->toDateString(), $booking->fresh()->start_date->toDateString());

        $cancel = $this->actingAs($this->customer)->patch(route('bookings.hotels.cancel', $booking));

        $cancel->assertSessionHasNoErrors();
        $this->assertSame('canceled', $booking->fresh()->status);
    }

    public function test_customer_can_complete_full_ferry_booking_lifecycle(): void
    {
        $ferry = Ferry::create([
            'user_id' => $this->owner->id,
            'name' => 'Moonwake Line',
            'description' => 'Picnic island crossing',
            'price' => 60,
            'max_capacity' => 40,
            'max_booking_quantity' => 4,
            'island_id' => $this->picnicIsland->id,
        ]);

        $create = $this->actingAs($this->customer)->post(route('bookings.ferries.store', $ferry), [
            'booking_time' => now()->addDays(5)->setTime(10, 0)->format('Y-m-d H:i:s'),
            'quantity' => 2,
        ]);

        $create->assertSessionHasNoErrors();

        $booking = FerryBooking::query()->firstOrFail();

        $reschedule = $this->actingAs($this->customer)->patch(route('bookings.ferries.reschedule', $booking), [
            'booking_time' => now()->addDays(6)->setTime(11, 0)->format('Y-m-d H:i:s'),
        ]);

        $reschedule->assertSessionHasNoErrors();
        $this->assertSame('11', $booking->fresh()->booking_time->format('H'));

        $cancel = $this->actingAs($this->customer)->patch(route('bookings.ferries.cancel', $booking));

        $cancel->assertSessionHasNoErrors();
        $this->assertSame('canceled', $booking->fresh()->status);
    }

    public function test_customer_can_complete_full_ride_booking_lifecycle(): void
    {
        $this->createConfirmedHotelStay(now()->addDays(5), now()->addDays(9));

        $ride = Ride::create([
            'user_id' => $this->owner->id,
            'island_id' => $this->horrorIsland->id,
            'name' => 'Velvet Spiral',
            'description' => 'Steel and velvet.',
            'price' => 165,
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
            'max_capacity' => 22,
            'max_booking_quantity' => 4,
        ]);

        $create = $this->actingAs($this->customer)->post(route('bookings.rides.store', $ride), [
            'booking_time' => now()->addDays(6)->setTime(9, 0)->format('Y-m-d H:i:s'),
            'quantity' => 2,
        ]);

        $create->assertSessionHasNoErrors();

        $booking = RideBooking::query()->firstOrFail();

        $reschedule = $this->actingAs($this->customer)->patch(route('bookings.rides.reschedule', $booking), [
            'booking_time' => now()->addDays(7)->setTime(17, 0)->format('Y-m-d H:i:s'),
        ]);

        $reschedule->assertSessionHasNoErrors();
        $this->assertSame('17', $booking->fresh()->booking_time->format('H'));

        $cancel = $this->actingAs($this->customer)->patch(route('bookings.rides.cancel', $booking));

        $cancel->assertSessionHasNoErrors();
        $this->assertSame('canceled', $booking->fresh()->status);
    }

    public function test_customer_can_complete_full_game_booking_lifecycle(): void
    {
        $this->createConfirmedHotelStay(now()->addDays(5), now()->addDays(9));

        $game = Game::create([
            'user_id' => $this->owner->id,
            'island_id' => $this->horrorIsland->id,
            'name' => 'Lantern Guess',
            'description' => 'Midnight trial.',
            'price' => 55,
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
            'max_capacity' => 22,
            'max_booking_quantity' => 4,
        ]);

        $create = $this->actingAs($this->customer)->post(route('bookings.games.store', $game), [
            'booking_time' => now()->addDays(6)->setTime(9, 0)->format('Y-m-d H:i:s'),
            'quantity' => 2,
        ]);

        $create->assertSessionHasNoErrors();

        $booking = GameBooking::query()->firstOrFail();

        $reschedule = $this->actingAs($this->customer)->patch(route('bookings.games.reschedule', $booking), [
            'booking_time' => now()->addDays(7)->setTime(17, 0)->format('Y-m-d H:i:s'),
        ]);

        $reschedule->assertSessionHasNoErrors();
        $this->assertSame('17', $booking->fresh()->booking_time->format('H'));

        $cancel = $this->actingAs($this->customer)->patch(route('bookings.games.cancel', $booking));

        $cancel->assertSessionHasNoErrors();
        $this->assertSame('canceled', $booking->fresh()->status);
    }

    public function test_customer_can_complete_full_beach_event_booking_lifecycle(): void
    {
        $eventDate = now()->addDays(8)->toDateString();

        $event = BeachEvent::create([
            'user_id' => $this->owner->id,
            'island_id' => $this->picnicIsland->id,
            'name' => 'Moonlight Vigil',
            'description' => 'Moonlit gathering.',
            'event_date' => $eventDate,
            'price' => 120,
            'max_capacity' => 80,
            'max_booking_quantity' => 4,
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
        ]);

        $create = $this->actingAs($this->customer)->post(route('bookings.beach-events.store', $event), [
            'booking_date' => $eventDate,
            'booking_time' => '19:00',
            'quantity' => 2,
        ]);

        $create->assertSessionHasNoErrors();

        $booking = BeachEventBooking::query()->firstOrFail();

        $reschedule = $this->actingAs($this->customer)->patch(route('bookings.beach-events.reschedule', $booking), [
            'booking_date' => $eventDate,
            'booking_time' => '20:00',
        ]);

        $reschedule->assertSessionHasNoErrors();
        $this->assertStringEndsWith('20:00:00', $booking->fresh()->getRawOriginal('booking_time'));

        $cancel = $this->actingAs($this->customer)->patch(route('bookings.beach-events.cancel', $booking));

        $cancel->assertSessionHasNoErrors();
        $this->assertSame('canceled', $booking->fresh()->status);
    }

    private function createHotel(string $name): Hotel
    {
        return Hotel::create([
            'user_id' => $this->owner->id,
            'name' => $name,
            'location' => 'Manor Ward',
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
        ]);
    }

    private function createConfirmedHotelStay(\Carbon\CarbonInterface $startDate, \Carbon\CarbonInterface $endDate): HotelBooking
    {
        $hotel = $this->createHotel('Access Hotel');

        $room = Room::create([
            'hotel_id' => $hotel->id,
            'room_number' => 'AX-01',
            'price' => 120,
            'status' => 'available',
            'max_occupancy' => 2,
            'amenities' => [],
            'images' => [],
            'description' => 'Access room',
        ]);

        return HotelBooking::create([
            'user_id' => $this->customer->id,
            'room_id' => $room->id,
            'start_date' => $startDate->copy()->startOfDay(),
            'end_date' => $endDate->copy()->startOfDay(),
            'quantity' => 1,
            'total_price' => 240,
            'status' => 'confirmed',
        ]);
    }
}
