<?php

namespace Tests\Feature;

use App\Models\Ferry;
use App\Models\FerryBooking;
use App\Models\Island;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PromotionsAndFerryOperationsTest extends TestCase
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

    public function test_homepage_only_renders_active_promotions(): void
    {
        Promotion::create([
            'title' => 'Moonlit Escape',
            'description' => 'A limited offer for haunted harbor stays.',
            'discount_percentage' => 15,
            'cta_label' => 'Reserve now',
            'cta_url' => '/hotels',
            'is_active' => true,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDay(),
        ]);

        Promotion::create([
            'title' => 'Expired Offer',
            'description' => 'This should not be visible.',
            'is_active' => true,
            'starts_at' => now()->subDays(4),
            'ends_at' => now()->subDay(),
        ]);

        Promotion::create([
            'title' => 'Draft Offer',
            'description' => 'This should stay hidden.',
            'is_active' => false,
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSeeText('Moonlit Escape');
        $response->assertDontSeeText('Expired Offer');
        $response->assertDontSeeText('Draft Offer');
    }

    public function test_ferry_booking_generates_a_separate_ferry_pass_and_download_endpoint(): void
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();
        $island = $this->createIsland('Picnic Island', 'Picnic-Island');

        $ferry = Ferry::create([
            'user_id' => $owner->id,
            'name' => 'Picnic Shuttle',
            'price' => 35,
            'max_capacity' => 100,
            'max_booking_quantity' => 5,
            'island_id' => $island->id,
        ]);

        $response = $this->actingAs($user)->post(route('bookings.ferries.store', $ferry), [
            'booking_time' => now()->addDay()->setTime(10, 0)->format('Y-m-d H:i:s'),
            'quantity' => 2,
        ]);

        $response->assertSessionHasNoErrors();

        $booking = FerryBooking::first();

        $this->assertNotNull($booking);
        $this->assertNotNull($booking->pass_number);
        $this->assertNotNull($booking->pass_path);
        Storage::disk('local')->assertExists($booking->pass_path);

        $download = $this->actingAs($user)->get(route('bookings.ferries.pass', $booking));

        $download->assertOk();
    }

    public function test_ferry_manager_passenger_report_only_shows_owned_manifest_rows(): void
    {
        $operator = User::factory()->create();
        $otherOperator = User::factory()->create();
        $customer = User::factory()->create(['name' => 'Visible Passenger']);
        $otherCustomer = User::factory()->create(['name' => 'Hidden Passenger']);
        $island = $this->createIsland('Horror Island', 'Horror-Island');

        Role::findOrCreate('ferry_manager', 'web');
        $operator->assignRole('ferry_manager');

        $ownedFerry = Ferry::create([
            'user_id' => $operator->id,
            'name' => 'Operator Ferry',
            'price' => 40,
            'max_capacity' => 100,
            'max_booking_quantity' => 5,
            'island_id' => $island->id,
        ]);

        $otherFerry = Ferry::create([
            'user_id' => $otherOperator->id,
            'name' => 'Other Ferry',
            'price' => 45,
            'max_capacity' => 100,
            'max_booking_quantity' => 5,
            'island_id' => $island->id,
        ]);

        FerryBooking::create([
            'user_id' => $customer->id,
            'ferry_id' => $ownedFerry->id,
            'booking_time' => now()->setTime(10, 0),
            'quantity' => 2,
            'total_price' => 80,
            'status' => 'confirmed',
            'pass_number' => 'PASS-VISIBLE',
            'pass_path' => 'ferry-passes/PASS-VISIBLE.pdf',
        ]);

        FerryBooking::create([
            'user_id' => $otherCustomer->id,
            'ferry_id' => $otherFerry->id,
            'booking_time' => now()->setTime(10, 0),
            'quantity' => 1,
            'total_price' => 45,
            'status' => 'confirmed',
            'pass_number' => 'PASS-HIDDEN',
            'pass_path' => 'ferry-passes/PASS-HIDDEN.pdf',
        ]);

        $response = $this->actingAs($operator)->get(route('ferry-reports.index', [
            'date' => now()->toDateString(),
        ]));

        $response->assertOk();
        $response->assertSeeText('Visible Passenger');
        $response->assertDontSeeText('Hidden Passenger');
        $response->assertSeeText('Operator Ferry');
        $response->assertDontSeeText('Other Ferry');
    }

    public function test_ferry_manager_can_export_owned_manifest_as_csv(): void
    {
        $operator = User::factory()->create();
        $customer = User::factory()->create(['name' => 'CSV Passenger', 'email' => 'csv@example.com']);
        $island = $this->createIsland('Picnic Island', 'Picnic-Island');

        Role::findOrCreate('ferry_manager', 'web');
        $operator->assignRole('ferry_manager');

        $ferry = Ferry::create([
            'user_id' => $operator->id,
            'name' => 'CSV Ferry',
            'price' => 32,
            'max_capacity' => 100,
            'max_booking_quantity' => 5,
            'island_id' => $island->id,
        ]);

        FerryBooking::create([
            'user_id' => $customer->id,
            'ferry_id' => $ferry->id,
            'booking_time' => now()->setTime(11, 0),
            'quantity' => 3,
            'total_price' => 96,
            'status' => 'confirmed',
            'pass_number' => 'PASS-CSV',
            'pass_path' => 'ferry-passes/PASS-CSV.pdf',
        ]);

        $response = $this->actingAs($operator)->get(route('ferry-reports.export', [
            'date' => now()->toDateString(),
        ]));

        $response->assertOk();
        $content = $response->streamedContent();

        $this->assertStringContainsString('CSV Passenger', $content);
        $this->assertStringContainsString('CSV Ferry', $content);
        $this->assertStringContainsString('PASS-CSV', $content);
    }

    private function createIsland(string $name, string $type): Island
    {
        return Island::create([
            'name' => $name,
            'type' => $type,
            'description' => $name.' description',
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
        ]);
    }
}
