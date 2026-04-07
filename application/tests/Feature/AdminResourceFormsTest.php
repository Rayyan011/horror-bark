<?php

namespace Tests\Feature;

use App\Filament\Resources\BeachEventResource\Pages\CreateBeachEvent;
use App\Filament\Resources\BeachEventResource\Pages\EditBeachEvent;
use App\Filament\Resources\FerryResource\Pages\CreateFerry;
use App\Filament\Resources\FerryResource\Pages\EditFerry;
use App\Filament\Resources\GameResource\Pages\CreateGame;
use App\Filament\Resources\GameResource\Pages\EditGame;
use App\Filament\Resources\HotelResource\Pages\CreateHotel;
use App\Filament\Resources\HotelResource\Pages\EditHotel;
use App\Filament\Resources\IslandResource\Pages\CreateIsland;
use App\Filament\Resources\IslandResource\Pages\EditIsland;
use App\Filament\Resources\RideResource\Pages\CreateRide;
use App\Filament\Resources\RideResource\Pages\EditRide;
use App\Filament\Resources\RoomResource\Pages\CreateRoom;
use App\Filament\Resources\RoomResource\Pages\EditRoom;
use App\Models\BeachEvent;
use App\Models\Ferry;
use App\Models\Game;
use App\Models\Hotel;
use App\Models\Island;
use App\Models\Ride;
use App\Models\Room;
use App\Models\User;
use App\Services\IslandAccessService;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminResourceFormsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $owner;

    protected Island $horrorIsland;

    protected Island $picnicIsland;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->admin = User::factory()->create();
        Role::findOrCreate('admin', 'web');
        $this->admin->assignRole('admin');

        $this->owner = User::factory()->create();

        $this->horrorIsland = Island::create([
            'name' => 'Shadow Park',
            'type' => IslandAccessService::HORROR_ISLAND,
            'description' => 'Theme park district',
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
        ]);

        $this->picnicIsland = Island::create([
            'name' => 'Pale Moon Strand',
            'type' => IslandAccessService::PICNIC_ISLAND,
            'description' => 'Shore district',
            'latitude' => 4.21,
            'longitude' => 73.41,
            'images' => [],
        ]);

        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Filament::bootCurrentPanel();
        $this->actingAs($this->admin);
    }

    public static function createResourceProvider(): array
    {
        return [
            'hotel' => ['hotel', CreateHotel::class],
            'room' => ['room', CreateRoom::class],
            'ride' => ['ride', CreateRide::class],
            'game' => ['game', CreateGame::class],
            'beach event' => ['beach_event', CreateBeachEvent::class],
            'ferry' => ['ferry', CreateFerry::class],
            'island' => ['island', CreateIsland::class],
        ];
    }

    #[DataProvider('createResourceProvider')]
    public function test_admin_can_create_resource_records(string $scenario, string $pageClass): void
    {
        Livewire::test($pageClass)
            ->fillForm($this->createPayload($scenario))
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertCreated($scenario);
    }

    public static function editResourceProvider(): array
    {
        return [
            'hotel' => ['hotel', EditHotel::class],
            'room' => ['room', EditRoom::class],
            'ride' => ['ride', EditRide::class],
            'game' => ['game', EditGame::class],
            'beach event' => ['beach_event', EditBeachEvent::class],
            'ferry' => ['ferry', EditFerry::class],
            'island' => ['island', EditIsland::class],
        ];
    }

    #[DataProvider('editResourceProvider')]
    public function test_admin_can_edit_resource_records(string $scenario, string $pageClass): void
    {
        $record = $this->existingRecord($scenario);

        Livewire::test($pageClass, ['record' => $record->getRouteKey()])
            ->fillForm($this->editPayload($scenario))
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertEdited($scenario, $record->fresh());
    }

    private function createPayload(string $scenario): array
    {
        return match ($scenario) {
            'hotel' => [
                'user_id' => $this->owner->id,
                'name' => 'Mourning House',
                'location' => 'Coven Quay',
                'description' => 'Harbor-side accommodation.',
                'map_x' => 44,
                'map_y' => 55,
                'latitude' => 4.22,
                'longitude' => 73.44,
            ],
            'room' => [
                'hotel_id' => $this->seedHotel()->id,
                'description' => 'Gallery suite',
                'images' => [UploadedFile::fake()->image('room.png')],
                'amenities' => ['Lantern service', 'Stone bath'],
                'room_number' => 'MH-102',
                'price' => 340,
                'max_occupancy' => 2,
                'status' => 'available',
            ],
            'ride' => [
                'user_id' => $this->owner->id,
                'name' => 'Ashfall Chute',
                'description' => 'Drop ride.',
                'price' => 170,
                'max_capacity' => 20,
                'max_booking_quantity' => 4,
                'island_id' => $this->horrorIsland->id,
                'map_x' => 38,
                'map_y' => 52,
                'latitude' => 4.2,
                'longitude' => 73.4,
            ],
            'game' => [
                'user_id' => $this->owner->id,
                'name' => 'Sigil Toss',
                'description' => 'Carnival game.',
                'price' => 40,
                'max_capacity' => 18,
                'max_booking_quantity' => 4,
                'island_id' => $this->horrorIsland->id,
                'map_x' => 48,
                'map_y' => 48,
                'latitude' => 4.2,
                'longitude' => 73.4,
            ],
            'beach_event' => [
                'user_id' => $this->owner->id,
                'name' => 'Lantern Choir',
                'description' => 'Beach ceremony.',
                'event_date' => now()->addWeek()->toDateString(),
                'price' => 98,
                'max_capacity' => 80,
                'max_booking_quantity' => 4,
                'island_id' => $this->picnicIsland->id,
                'map_x' => 72,
                'map_y' => 66,
                'latitude' => 4.21,
                'longitude' => 73.41,
            ],
            'ferry' => [
                'user_id' => $this->owner->id,
                'name' => 'Blackwater Bell',
                'description' => 'Night crossing.',
                'price' => 75,
                'max_capacity' => 36,
                'max_booking_quantity' => 6,
                'island_id' => $this->picnicIsland->id,
                'map_x' => 61,
                'map_y' => 59,
            ],
            'island' => [
                'name' => 'Cinder Strand',
                'type' => IslandAccessService::PICNIC_ISLAND,
                'description' => 'A new shore district.',
                'map_x' => 78,
                'map_y' => 62,
                'latitude' => 4.23,
                'longitude' => 73.42,
            ],
        };
    }

    private function editPayload(string $scenario): array
    {
        return match ($scenario) {
            'hotel' => [
                'name' => 'Mourning House Revised',
                'location' => 'Blackwater Shore',
                'description' => 'Updated harbor-side accommodation.',
                'map_x' => 49,
                'map_y' => 57,
            ],
            'room' => [
                'description' => 'Updated gallery suite',
                'images' => ['rooms/existing-room.png'],
                'amenities' => ['Lantern service', 'Private supper'],
                'room_number' => 'MH-204',
                'price' => 360,
                'max_occupancy' => 3,
                'status' => 'available',
            ],
            'ride' => [
                'name' => 'Ashfall Chute Revised',
                'description' => 'Updated drop ride.',
                'price' => 180,
                'max_capacity' => 24,
                'max_booking_quantity' => 4,
                'island_id' => $this->horrorIsland->id,
            ],
            'game' => [
                'name' => 'Sigil Toss Revised',
                'description' => 'Updated carnival game.',
                'price' => 44,
                'max_capacity' => 22,
                'max_booking_quantity' => 5,
                'island_id' => $this->horrorIsland->id,
            ],
            'beach_event' => [
                'name' => 'Lantern Choir Revised',
                'description' => 'Updated beach ceremony.',
                'event_date' => now()->addDays(9)->toDateString(),
                'price' => 104,
                'max_capacity' => 84,
                'max_booking_quantity' => 5,
                'island_id' => $this->picnicIsland->id,
            ],
            'ferry' => [
                'name' => 'Blackwater Bell Revised',
                'description' => 'Updated night crossing.',
                'price' => 82,
                'max_capacity' => 40,
                'max_booking_quantity' => 6,
                'island_id' => $this->picnicIsland->id,
            ],
            'island' => [
                'name' => 'Cinder Strand Revised',
                'type' => IslandAccessService::PICNIC_ISLAND,
                'description' => 'Updated shore district.',
            ],
        };
    }

    private function assertCreated(string $scenario): void
    {
        match ($scenario) {
            'hotel' => $this->assertDatabaseHas('hotels', ['name' => 'Mourning House']),
            'room' => $this->assertDatabaseHas('rooms', ['room_number' => 'MH-102']),
            'ride' => $this->assertDatabaseHas('rides', ['name' => 'Ashfall Chute']),
            'game' => $this->assertDatabaseHas('games', ['name' => 'Sigil Toss']),
            'beach_event' => $this->assertDatabaseHas('beach_events', ['name' => 'Lantern Choir']),
            'ferry' => $this->assertDatabaseHas('ferries', ['name' => 'Blackwater Bell']),
            'island' => $this->assertDatabaseHas('islands', ['name' => 'Cinder Strand']),
        };
    }

    private function assertEdited(string $scenario, object $record): void
    {
        match ($scenario) {
            'hotel' => $this->assertSame('Mourning House Revised', $record->name),
            'room' => $this->assertSame('MH-204', $record->room_number),
            'ride' => $this->assertSame('Ashfall Chute Revised', $record->name),
            'game' => $this->assertSame('Sigil Toss Revised', $record->name),
            'beach_event' => $this->assertSame('Lantern Choir Revised', $record->name),
            'ferry' => $this->assertSame('Blackwater Bell Revised', $record->name),
            'island' => $this->assertSame('Cinder Strand Revised', $record->name),
        };
    }

    private function existingRecord(string $scenario): object
    {
        return match ($scenario) {
            'hotel' => Hotel::create([
                'user_id' => $this->owner->id,
                'name' => 'Old Hotel',
                'location' => 'Manor Ward',
                'description' => 'Existing hotel',
                'latitude' => 4.2,
                'longitude' => 73.4,
                'images' => [],
            ]),
            'room' => Room::create([
                'hotel_id' => $this->seedHotel()->id,
                'room_number' => 'OLD-1',
                'price' => 220,
                'status' => 'available',
                'max_occupancy' => 2,
                'amenities' => ['Desk'],
                'images' => ['rooms/existing-room.png'],
                'description' => 'Existing room',
            ]),
            'ride' => Ride::create([
                'user_id' => $this->owner->id,
                'island_id' => $this->horrorIsland->id,
                'name' => 'Old Ride',
                'description' => 'Existing ride',
                'price' => 120,
                'latitude' => 4.2,
                'longitude' => 73.4,
                'images' => [],
                'max_capacity' => 16,
                'max_booking_quantity' => 4,
            ]),
            'game' => Game::create([
                'user_id' => $this->owner->id,
                'island_id' => $this->horrorIsland->id,
                'name' => 'Old Game',
                'description' => 'Existing game',
                'price' => 30,
                'latitude' => 4.2,
                'longitude' => 73.4,
                'images' => [],
                'max_capacity' => 16,
                'max_booking_quantity' => 4,
            ]),
            'beach_event' => BeachEvent::create([
                'user_id' => $this->owner->id,
                'island_id' => $this->picnicIsland->id,
                'name' => 'Old Event',
                'description' => 'Existing event',
                'event_date' => now()->addWeek()->toDateString(),
                'price' => 90,
                'max_capacity' => 60,
                'max_booking_quantity' => 4,
                'latitude' => 4.21,
                'longitude' => 73.41,
                'images' => [],
            ]),
            'ferry' => Ferry::create([
                'user_id' => $this->owner->id,
                'name' => 'Old Ferry',
                'description' => 'Existing ferry',
                'price' => 60,
                'max_capacity' => 30,
                'max_booking_quantity' => 5,
                'island_id' => $this->picnicIsland->id,
            ]),
            'island' => Island::create([
                'name' => 'Old Island',
                'type' => IslandAccessService::PICNIC_ISLAND,
                'description' => 'Existing island',
                'latitude' => 4.23,
                'longitude' => 73.42,
                'images' => [],
            ]),
        };
    }

    private function seedHotel(): Hotel
    {
        return Hotel::query()->first() ?? Hotel::create([
            'user_id' => $this->owner->id,
            'name' => 'Seed Hotel',
            'location' => 'Shadow Park',
            'description' => 'Seed hotel',
            'latitude' => 4.2,
            'longitude' => 73.4,
            'images' => [],
        ]);
    }
}
