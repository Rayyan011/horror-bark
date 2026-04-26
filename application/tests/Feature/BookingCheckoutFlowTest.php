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
use App\Services\IslandAccessService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BookingCheckoutFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Mail::fake();

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

    public function test_hotel_checkout_requires_confirmation_before_booking_creation_and_offers_qr_generation(): void
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();

        $hotel = Hotel::create([
            'user_id' => $owner->id,
            'name' => 'Nightfall Inn',
            'location' => 'Harbor',
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
        ]);

        $room = Room::create([
            'hotel_id' => $hotel->id,
            'room_number' => '101',
            'price' => 120,
            'status' => 'available',
            'max_occupancy' => 2,
            'images' => [],
        ]);

        $prepare = $this->actingAs($user)->post(route('checkout.hotels.prepare', $room), [
            'start_date' => now()->addDays(4)->toDateString(),
            'end_date' => now()->addDays(6)->toDateString(),
            'quantity' => 2,
        ]);

        $prepare->assertRedirect();
        $this->assertDatabaseCount('hotel_bookings', 0);

        $location = $prepare->headers->get('Location');
        $token = basename(parse_url($location, PHP_URL_PATH));

        $this->actingAs($user)->get($location)
            ->assertOk()
            ->assertSee('Review the booking, then pass through the demo payment gate.')
            ->assertSee('Generate Payment QR')
            ->assertSee('Demo Payment Gateway');

        $confirm = $this->actingAs($user)->post(route('checkout.confirm', $token), [
            'payment_method' => 'ghost_card',
            'cardholder_name' => $user->name,
            'card_number' => '4242424242424242',
            'expiry_month' => '12',
            'expiry_year' => '29',
            'security_code' => '123',
            'acknowledge_demo' => '1',
        ]);

        $confirm->assertRedirect();
        $this->assertDatabaseHas('hotel_bookings', [
            'user_id' => $user->id,
            'room_id' => $room->id,
            'status' => 'confirmed',
        ]);
        $this->assertDatabaseCount('invoices', 1);
    }

    public function test_every_booking_type_can_open_the_demo_checkout_page(): void
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();
        $hotel = Hotel::create([
            'user_id' => $owner->id,
            'name' => 'Access Hotel',
            'location' => 'Manor Ward',
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
        ]);
        $room = Room::create([
            'hotel_id' => $hotel->id,
            'room_number' => 'AX-01',
            'price' => 120,
            'status' => 'available',
            'max_occupancy' => 2,
            'images' => [],
        ]);
        HotelBooking::create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'start_date' => now()->addDays(4)->startOfDay(),
            'end_date' => now()->addDays(10)->startOfDay(),
            'quantity' => 1,
            'total_price' => 720,
            'status' => 'confirmed',
        ]);

        $picnicIsland = Island::create([
            'name' => 'Pale Moon Strand',
            'type' => IslandAccessService::PICNIC_ISLAND,
            'description' => 'Picnic district',
            'latitude' => 4.21,
            'longitude' => 73.41,
            'images' => [],
        ]);

        $ferry = Ferry::create([
            'user_id' => $owner->id,
            'name' => 'Moonwake Line',
            'description' => 'Picnic island crossing',
            'price' => 60,
            'max_capacity' => 40,
            'max_booking_quantity' => 4,
            'island_id' => $picnicIsland->id,
        ]);

        $ride = Ride::create([
            'user_id' => $owner->id,
            'island_id' => $picnicIsland->id,
            'name' => 'Velvet Spiral',
            'description' => 'Steel and velvet.',
            'price' => 165,
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
            'max_capacity' => 22,
            'max_booking_quantity' => 4,
        ]);

        $game = Game::create([
            'user_id' => $owner->id,
            'island_id' => $picnicIsland->id,
            'name' => 'Lantern Guess',
            'description' => 'Midnight trial.',
            'price' => 55,
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
            'max_capacity' => 22,
            'max_booking_quantity' => 4,
        ]);

        $eventDate = now()->addDays(8)->toDateString();
        $event = BeachEvent::create([
            'user_id' => $owner->id,
            'island_id' => $picnicIsland->id,
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

        $scenarios = [
            [
                'url' => route('checkout.ferries.prepare', $ferry),
                'payload' => [
                    'booking_time' => now()->addDays(5)->setTime(10, 0)->format('Y-m-d H:i:s'),
                    'quantity' => 2,
                ],
            ],
            [
                'url' => route('checkout.rides.prepare', $ride),
                'payload' => [
                    'booking_time' => now()->addDays(6)->setTime(9, 0)->format('Y-m-d H:i:s'),
                    'quantity' => 2,
                ],
            ],
            [
                'url' => route('checkout.games.prepare', $game),
                'payload' => [
                    'booking_time' => now()->addDays(6)->setTime(17, 0)->format('Y-m-d H:i:s'),
                    'quantity' => 2,
                ],
            ],
            [
                'url' => route('checkout.beach-events.prepare', $event),
                'payload' => [
                    'booking_date' => $eventDate,
                    'booking_time' => '19:00',
                    'quantity' => 2,
                ],
            ],
        ];

        foreach ($scenarios as $scenario) {
            $response = $this->actingAs($user)->post($scenario['url'], $scenario['payload']);

            $response->assertRedirect();
            $this->actingAs($user)->get($response->headers->get('Location'))
                ->assertOk()
                ->assertSee('Generate Payment QR')
                ->assertSee('Demo Payment Gateway');
        }
    }
}
