<?php

namespace Tests\Feature;

use App\Filament\Resources\AuditLogResource;
use App\Mail\BookingChangedMail;
use App\Mail\BookingConfirmationMail;
use App\Mail\BookingReminderMail;
use App\Models\AuditLog;
use App\Models\Ferry;
use App\Models\FerryBooking;
use App\Models\Hotel;
use App\Models\HotelBooking;
use App\Models\Island;
use App\Models\Room;
use App\Models\User;
use App\Services\BookingLifecycleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BookingLifecycleAndReportingTest extends TestCase
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

    public function test_booking_confirmation_sends_email_and_writes_audit_log(): void
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();
        $hotel = $this->createHotel($owner);
        $room = Room::create([
            'hotel_id' => $hotel->id,
            'room_number' => 'A-101',
            'price' => 120,
            'status' => 'available',
            'max_occupancy' => 4,
            'amenities' => [],
            'images' => [],
            'description' => 'Harbor room',
        ]);

        $response = $this->actingAs($user)->post(route('bookings.hotels.store', $room), [
            'start_date' => now()->addDays(4)->toDateString(),
            'end_date' => now()->addDays(6)->toDateString(),
            'quantity' => 2,
        ]);

        $response->assertSessionHasNoErrors();
        $booking = HotelBooking::with('invoice')->firstOrFail();

        $this->assertNotNull($booking->invoice);
        Mail::assertQueued(BookingConfirmationMail::class, 1);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'booking.confirmed',
            'auditable_type' => HotelBooking::class,
            'auditable_id' => $booking->id,
        ]);
    }

    public function test_booking_confirmation_failure_does_not_abort_booking_creation(): void
    {
        Mail::swap(app('mail.manager'));
        config([
            'mail.default' => 'array',
            'mail.from.address' => '[email protected]',
        ]);

        $user = User::factory()->create();
        $owner = User::factory()->create();
        $hotel = $this->createHotel($owner);
        $room = Room::create([
            'hotel_id' => $hotel->id,
            'room_number' => 'A-404',
            'price' => 160,
            'status' => 'available',
            'max_occupancy' => 2,
            'amenities' => [],
            'images' => [],
            'description' => 'Mail failure room',
        ]);

        $response = $this->actingAs($user)->post(route('bookings.hotels.store', $room), [
            'start_date' => now()->addDays(4)->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'quantity' => 1,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $this->assertDatabaseHas('hotel_bookings', [
            'user_id' => $user->id,
            'room_id' => $room->id,
            'status' => 'confirmed',
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'booking.confirmed',
            'auditable_type' => HotelBooking::class,
        ]);
    }

    public function test_customer_can_reschedule_ferry_and_receives_change_email(): void
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();
        $island = $this->createIsland('Picnic Island', 'Picnic-Island');
        $ferry = $this->createFerry($owner, $island, 'Reschedule Ferry');

        $booking = FerryBooking::create([
            'user_id' => $user->id,
            'ferry_id' => $ferry->id,
            'booking_time' => now()->addDays(3)->setTime(10, 0),
            'quantity' => 2,
            'total_price' => 80,
            'status' => 'confirmed',
        ]);

        app(BookingLifecycleService::class)->createConfirmedBooking($booking, $user);

        $response = $this->actingAs($user)->patch(route('bookings.ferries.reschedule', $booking), [
            'booking_time' => now()->addDays(4)->setTime(11, 0)->format('Y-m-d H:i:s'),
        ]);

        $response->assertSessionHasNoErrors();
        $booking->refresh();

        $this->assertSame('11', $booking->booking_time->format('H'));
        Mail::assertQueued(BookingChangedMail::class, 1);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'booking.rescheduled',
            'auditable_type' => FerryBooking::class,
            'auditable_id' => $booking->id,
        ]);
        Storage::disk('local')->assertExists($booking->pass_path);
    }

    public function test_customer_cannot_cancel_booking_inside_cutoff_window(): void
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();
        $island = $this->createIsland('Picnic Island', 'Picnic-Island');
        $ferry = $this->createFerry($owner, $island, 'Cutoff Ferry');

        $booking = FerryBooking::create([
            'user_id' => $user->id,
            'ferry_id' => $ferry->id,
            'booking_time' => now()->addHours(12)->setTime(10, 0),
            'quantity' => 1,
            'total_price' => 40,
            'status' => 'confirmed',
        ]);

        app(BookingLifecycleService::class)->createConfirmedBooking($booking, $user);

        $response = $this->actingAs($user)->from(route('bookings.ferries.show', $booking))
            ->patch(route('bookings.ferries.cancel', $booking));

        $response->assertSessionHasErrors('booking');
        $this->assertSame('confirmed', $booking->fresh()->status);
    }

    public function test_reschedule_is_blocked_when_hotel_access_rule_is_no_longer_met(): void
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();
        $island = $this->createIsland('Horror Island', 'Horror-Island');
        $hotel = $this->createHotel($owner);
        $room = Room::create([
            'hotel_id' => $hotel->id,
            'room_number' => 'H-201',
            'price' => 150,
            'status' => 'available',
            'max_occupancy' => 2,
            'amenities' => [],
            'images' => [],
            'description' => 'Night room',
        ]);

        HotelBooking::create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'start_date' => now()->addDays(2)->startOfDay(),
            'end_date' => now()->addDays(4)->startOfDay(),
            'quantity' => 1,
            'total_price' => 300,
            'status' => 'confirmed',
        ]);

        $ferry = $this->createFerry($owner, $island, 'Horror Shuttle');
        $booking = FerryBooking::create([
            'user_id' => $user->id,
            'ferry_id' => $ferry->id,
            'booking_time' => now()->addDays(3)->setTime(10, 0),
            'quantity' => 1,
            'total_price' => 40,
            'status' => 'confirmed',
        ]);

        app(BookingLifecycleService::class)->createConfirmedBooking($booking, $user);

        $response = $this->actingAs($user)->from(route('bookings.ferries.show', $booking))
            ->patch(route('bookings.ferries.reschedule', $booking), [
                'booking_time' => now()->addDays(8)->setTime(10, 0)->format('Y-m-d H:i:s'),
            ]);

        $response->assertSessionHasErrors('booking_time');
        $this->assertSame(now()->addDays(3)->format('Y-m-d 10:00:00'), $booking->fresh()->booking_time->format('Y-m-d H:i:s'));
    }

    public function test_booking_reminder_command_sends_once(): void
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();
        $island = $this->createIsland('Picnic Island', 'Picnic-Island');
        $ferry = $this->createFerry($owner, $island, 'Reminder Ferry');

        $booking = FerryBooking::create([
            'user_id' => $user->id,
            'ferry_id' => $ferry->id,
            'booking_time' => now()->addDay()->startOfHour(),
            'quantity' => 1,
            'total_price' => 40,
            'status' => 'confirmed',
        ]);

        app(BookingLifecycleService::class)->createConfirmedBooking($booking, $user);

        $this->artisan('bookings:send-reminders')->assertExitCode(0);
        $this->artisan('bookings:send-reminders')->assertExitCode(0);

        Mail::assertQueued(BookingReminderMail::class, 1);
        $this->assertNotNull($booking->fresh()->reminder_sent_at);
    }

    public function test_hotel_reminder_command_does_not_depend_on_midnight_run(): void
    {
        $this->travelTo(now()->startOfDay()->addHours(10));

        $user = User::factory()->create();
        $owner = User::factory()->create();
        $hotel = $this->createHotel($owner);
        $room = Room::create([
            'hotel_id' => $hotel->id,
            'room_number' => 'R-24',
            'price' => 120,
            'status' => 'available',
            'max_occupancy' => 2,
            'amenities' => [],
            'images' => [],
            'description' => 'Reminder room',
        ]);

        $booking = HotelBooking::create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'start_date' => now()->addDay()->startOfDay(),
            'end_date' => now()->addDays(2)->startOfDay(),
            'quantity' => 1,
            'total_price' => 120,
            'status' => 'confirmed',
        ]);

        app(BookingLifecycleService::class)->createConfirmedBooking($booking, $user);
        Mail::assertQueued(BookingConfirmationMail::class, 1);

        $this->artisan('bookings:send-reminders')->assertExitCode(0);

        Mail::assertQueued(BookingReminderMail::class, 1);
        $this->assertNotNull($booking->fresh()->reminder_sent_at);
    }

    public function test_admin_report_export_contains_multiple_booking_types(): void
    {
        $admin = User::factory()->create();
        Role::findOrCreate('admin', 'web');
        $admin->assignRole('admin');

        $customer = User::factory()->create();
        $owner = User::factory()->create();
        $hotel = $this->createHotel($owner);
        $room = Room::create([
            'hotel_id' => $hotel->id,
            'room_number' => 'R-1',
            'price' => 100,
            'status' => 'available',
            'max_occupancy' => 2,
            'amenities' => [],
            'images' => [],
            'description' => 'Room',
        ]);
        $hotelBooking = HotelBooking::create([
            'user_id' => $customer->id,
            'room_id' => $room->id,
            'start_date' => now()->addDays(4)->startOfDay(),
            'end_date' => now()->addDays(5)->startOfDay(),
            'quantity' => 1,
            'total_price' => 100,
            'status' => 'confirmed',
        ]);
        app(BookingLifecycleService::class)->createConfirmedBooking($hotelBooking, $customer);

        $ferry = $this->createFerry($owner, $this->createIsland('Picnic 2', 'Picnic-Island'), 'Admin Ferry');
        $ferryBooking = FerryBooking::create([
            'user_id' => $customer->id,
            'ferry_id' => $ferry->id,
            'booking_time' => now()->addDays(4)->setTime(11, 0),
            'quantity' => 1,
            'total_price' => 40,
            'status' => 'confirmed',
        ]);
        app(BookingLifecycleService::class)->createConfirmedBooking($ferryBooking, $customer);

        $response = $this->actingAs($admin)->get(route('admin-reports.export'));

        $response->assertOk();
        $content = $response->streamedContent();
        $this->assertStringContainsString($hotel->name, $content);
        $this->assertStringContainsString('Admin Ferry', $content);
    }

    public function test_operator_report_export_is_scoped_to_owned_inventory(): void
    {
        $manager = User::factory()->create();
        $otherManager = User::factory()->create();
        $customer = User::factory()->create(['name' => 'Owned Guest']);
        $otherCustomer = User::factory()->create(['name' => 'Hidden Guest']);

        Role::findOrCreate('hotel_manager', 'web');
        $manager->assignRole('hotel_manager');

        $ownedHotel = $this->createHotel($manager, 'Owned Hotel');
        $otherHotel = $this->createHotel($otherManager, 'Other Hotel');

        $ownedRoom = Room::create([
            'hotel_id' => $ownedHotel->id,
            'room_number' => 'O-1',
            'price' => 100,
            'status' => 'available',
            'max_occupancy' => 2,
            'amenities' => [],
            'images' => [],
            'description' => 'Owned room',
        ]);

        $otherRoom = Room::create([
            'hotel_id' => $otherHotel->id,
            'room_number' => 'X-1',
            'price' => 90,
            'status' => 'available',
            'max_occupancy' => 2,
            'amenities' => [],
            'images' => [],
            'description' => 'Other room',
        ]);

        HotelBooking::create([
            'user_id' => $customer->id,
            'room_id' => $ownedRoom->id,
            'start_date' => now()->addDays(5)->startOfDay(),
            'end_date' => now()->addDays(6)->startOfDay(),
            'quantity' => 1,
            'total_price' => 100,
            'status' => 'confirmed',
        ]);

        HotelBooking::create([
            'user_id' => $otherCustomer->id,
            'room_id' => $otherRoom->id,
            'start_date' => now()->addDays(5)->startOfDay(),
            'end_date' => now()->addDays(6)->startOfDay(),
            'quantity' => 1,
            'total_price' => 90,
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($manager)->get(route('operator-reports.export', ['domain' => 'hotel']));

        $response->assertOk();
        $content = $response->streamedContent();
        $this->assertStringContainsString('Owned Guest', $content);
        $this->assertStringContainsString('Owned Hotel', $content);
        $this->assertStringNotContainsString('Hidden Guest', $content);
        $this->assertStringNotContainsString('Other Hotel', $content);
    }

    public function test_legacy_ferry_report_route_still_uses_ferry_report_page(): void
    {
        $this->travelTo(now()->startOfDay()->addHours(9));

        $manager = User::factory()->create();

        Role::findOrCreate('ferry_manager', 'web');
        $manager->assignRole('ferry_manager');

        $island = $this->createIsland('Report Island', 'Picnic-Island');
        $ownedFerry = $this->createFerry($manager, $island, 'Owned Ferry');
        $otherOwnedFerry = $this->createFerry($manager, $island, 'Later Ferry');

        $response = $this->actingAs($manager)->get(route('ferry-reports.index', [
            'date' => now()->addDay()->toDateString(),
            'ferry_id' => $ownedFerry->id,
            'hour' => 10,
        ]));

        $response->assertOk();
        $response->assertSee('Ferry Passenger Reports');
        $response->assertSee('Departure Hour');
        $response->assertSee($ownedFerry->name);
        $response->assertSee($otherOwnedFerry->name);
    }

    public function test_audit_logs_are_admin_only(): void
    {
        $admin = User::factory()->create();
        $user = User::factory()->create();

        Role::findOrCreate('admin', 'web');
        $admin->assignRole('admin');

        $this->actingAs($admin);
        $this->assertTrue(AuditLogResource::canViewAny());

        $this->actingAs($user);
        $this->assertFalse(AuditLogResource::canViewAny());

        AuditLog::create([
            'actor_id' => $admin->id,
            'action' => 'booking.confirmed',
            'auditable_type' => HotelBooking::class,
            'auditable_id' => 1,
            'before_state' => null,
            'after_state' => ['status' => 'confirmed'],
            'metadata' => [],
            'occurred_at' => now(),
        ]);

        $this->actingAs($admin)->get('/admin/audit-logs')->assertOk();
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

    private function createHotel(User $owner, string $name = 'Haunted Hotel'): Hotel
    {
        return Hotel::create([
            'user_id' => $owner->id,
            'name' => $name,
            'location' => 'Harbor',
            'latitude' => 4.1,
            'longitude' => 73.2,
            'images' => [],
        ]);
    }

    private function createFerry(User $owner, Island $island, string $name): Ferry
    {
        return Ferry::create([
            'user_id' => $owner->id,
            'name' => $name,
            'price' => 40,
            'max_capacity' => 100,
            'max_booking_quantity' => 5,
            'island_id' => $island->id,
        ]);
    }
}
