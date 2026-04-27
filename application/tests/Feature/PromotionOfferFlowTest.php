<?php

namespace Tests\Feature;

use App\Models\BeachEvent;
use App\Models\Ferry;
use App\Models\Hotel;
use App\Models\Island;
use App\Models\Promotion;
use App\Models\Room;
use App\Models\User;
use App\Services\IslandAccessService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PromotionOfferFlowTest extends TestCase
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

    public function test_offer_page_lists_specific_discounted_rooms_for_hotel_promotion(): void
    {
        $owner = User::factory()->create();

        $coldstone = Hotel::create([
            'user_id' => $owner->id,
            'name' => 'Coldstone Chambers',
            'location' => 'Lantern Hollow',
            'description' => 'Lantern-lit quarter.',
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
        ]);

        Room::create([
            'hotel_id' => $coldstone->id,
            'room_number' => 'CC-101',
            'price' => 240,
            'status' => 'available',
            'max_occupancy' => 2,
            'images' => [],
        ]);

        Room::create([
            'hotel_id' => $coldstone->id,
            'room_number' => 'CC-214',
            'price' => 280,
            'status' => 'available',
            'max_occupancy' => 3,
            'images' => [],
        ]);

        $promotion = Promotion::create([
            'title' => 'Manor & Midway Arrangement',
            'description' => 'Discounted chamber offer.',
            'discount_percentage' => 15,
            'cta_label' => 'Open Offer',
            'cta_url' => '/hotels',
            'is_active' => true,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDay(),
        ]);

        $this->get(route('promotions.show', $promotion))
            ->assertOk()
            ->assertSeeText('Book The Discounted Selection')
            ->assertSeeText('CC-101')
            ->assertSeeText('CC-214')
            ->assertSeeText('Coldstone Chambers')
            ->assertSeeText('Log in to claim Moonlit Chamber Rates');
    }

    public function test_specific_hotel_promotion_path_renders_a_discounted_room_offer_page_instead_of_redirecting_to_hotel_profile(): void
    {
        $owner = User::factory()->create();

        $coldstone = Hotel::create([
            'user_id' => $owner->id,
            'name' => 'Coldstone Chambers',
            'location' => 'Lantern Hollow',
            'description' => 'Lantern-lit quarter.',
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
        ]);

        $velvetWake = Hotel::create([
            'user_id' => $owner->id,
            'name' => 'Velvet Wake House',
            'location' => 'Blackwater Approach',
            'description' => 'Harbor lodging.',
            'latitude' => 4.25,
            'longitude' => 73.45,
            'images' => [],
        ]);

        Room::create([
            'hotel_id' => $coldstone->id,
            'room_number' => 'CC-008',
            'price' => 460,
            'status' => 'available',
            'max_occupancy' => 2,
            'images' => [],
        ]);

        Room::create([
            'hotel_id' => $coldstone->id,
            'room_number' => 'CC-302',
            'price' => 510,
            'status' => 'available',
            'max_occupancy' => 4,
            'images' => [],
        ]);

        Room::create([
            'hotel_id' => $velvetWake->id,
            'room_number' => 'VW-301',
            'price' => 390,
            'status' => 'available',
            'max_occupancy' => 2,
            'images' => [],
        ]);

        $promotion = Promotion::create([
            'title' => 'Coldstone Chamber Promotion',
            'description' => 'Discounted Coldstone rooms.',
            'discount_percentage' => 18,
            'cta_label' => 'Open Offer',
            'cta_url' => '/hotels/'.$coldstone->id,
            'is_active' => true,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDay(),
        ]);

        $this->get(route('promotions.show', $promotion))
            ->assertOk()
            ->assertSeeText('Coldstone Chambers')
            ->assertSeeText('CC-008')
            ->assertSeeText('CC-302')
            ->assertDontSeeText('VW-301');
    }

    public function test_hotel_offer_discount_is_carried_into_checkout_and_booking_total(): void
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();

        $hotel = Hotel::create([
            'user_id' => $owner->id,
            'name' => 'Coldstone Chambers',
            'location' => 'Lantern Hollow',
            'description' => 'Lantern-lit quarter.',
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
        ]);

        $room = Room::create([
            'hotel_id' => $hotel->id,
            'room_number' => 'CC-101',
            'price' => 200,
            'status' => 'available',
            'max_occupancy' => 2,
            'images' => [],
        ]);

        $promotion = Promotion::create([
            'title' => 'Manor & Midway Arrangement',
            'description' => 'Discounted chamber offer.',
            'discount_percentage' => 20,
            'cta_label' => 'Open Offer',
            'cta_url' => '/hotels',
            'is_active' => true,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDay(),
        ]);

        $prepare = $this->actingAs($user)->post(route('checkout.hotels.prepare', $room), [
            'start_date' => now()->addDays(4)->toDateString(),
            'end_date' => now()->addDays(6)->toDateString(),
            'quantity' => 2,
            'promotion_id' => $promotion->id,
        ]);

        $prepare->assertRedirect();

        $location = $prepare->headers->get('Location');
        $token = basename(parse_url($location, PHP_URL_PATH));

        $this->actingAs($user)->get($location)
            ->assertOk()
            ->assertSeeText('Offer Applied')
            ->assertSeeText('Moonlit Chamber Rates')
            ->assertSeeText('MVR 640.00');

        $this->actingAs($user)->post(route('checkout.confirm', $token), [
            'payment_method' => 'ghost_card',
            'cardholder_name' => $user->name,
            'card_number' => '4242424242424242',
            'expiry_month' => '12',
            'expiry_year' => '29',
            'security_code' => '123',
            'payment_acknowledgement' => '1',
        ])->assertRedirect();

        $this->assertDatabaseHas('hotel_bookings', [
            'user_id' => $user->id,
            'room_id' => $room->id,
            'total_price' => 640,
            'status' => 'confirmed',
        ]);
    }

    public function test_legacy_ferry_promotion_path_falls_back_to_the_correct_public_route(): void
    {
        $owner = User::factory()->create();
        $island = Island::create([
            'name' => 'Coven Quay',
            'type' => IslandAccessService::PICNIC_ISLAND,
            'description' => 'Lantern harbor.',
            'latitude' => 4.21,
            'longitude' => 73.41,
            'images' => [],
        ]);

        Ferry::create([
            'user_id' => $owner->id,
            'name' => 'Moonwake Line',
            'description' => 'Blackwater passage.',
            'price' => 90,
            'max_capacity' => 40,
            'max_booking_quantity' => 4,
            'island_id' => $island->id,
            'images' => [],
        ]);

        BeachEvent::query()->delete();

        $promotion = Promotion::create([
            'title' => 'Passage Under The Pale Moon',
            'description' => 'Discounted ferry offer.',
            'discount_percentage' => 10,
            'cta_label' => 'Open Offer',
            'cta_url' => '/ferries',
            'is_active' => true,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDay(),
        ]);

        $this->get(route('promotions.show', $promotion))
            ->assertOk()
            ->assertSeeText('Moonwake Line');

        Ferry::query()->delete();

        $this->get(route('promotions.show', $promotion))
            ->assertRedirect(route('ferries.index'));
    }
}
