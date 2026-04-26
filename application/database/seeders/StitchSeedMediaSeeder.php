<?php

namespace Database\Seeders;

use App\Models\BeachEvent;
use App\Models\Ferry;
use App\Models\Game;
use App\Models\Hotel;
use App\Models\Island;
use App\Models\Ride;
use App\Models\Room;
use App\Models\User;
use App\Services\IslandAccessService;
use App\Support\HorrorGeneratedMediaCatalog;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StitchSeedMediaSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $owners = $this->seedOwners($now->copy());
        $islands = $this->seedIslands($now->copy());
        $hotels = $this->seedHotels($owners, $now->copy());

        $this->seedRooms($hotels);
        $this->seedRides($owners, $islands);
        $this->seedGames($owners, $islands);
        $this->seedBeachEvents($owners, $islands);
        $this->seedFerries($owners, $islands);
    }

    private function seedOwners(\Illuminate\Support\Carbon $now): array
    {
        $records = [
            'evelyn.thorne@horrorbark.test' => 'Evelyn Thorne',
            'silas.blackwood@horrorbark.test' => 'Silas Blackwood',
            'jasper.crowe@horrorbark.test' => 'Jasper Crowe',
            'ophelia.vale@horrorbark.test' => 'Ophelia Vale',
        ];

        $ids = [];

        foreach ($records as $email => $name) {
            $owner = User::query()->updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'email_verified_at' => $now,
                    'password' => Hash::make('test@admin.com'),
                    'remember_token' => null,
                ],
            );

            $ids[$email] = $owner->id;
        }

        return $ids;
    }

    private function seedIslands(\Illuminate\Support\Carbon $now): array
    {
        $records = [
            [
                'name' => 'Horror Island',
                'type' => IslandAccessService::HORROR_ISLAND,
                'description' => 'The main Horror Bark island, containing the manor, harbor, ride districts, game grounds, and guest hotels.',
                'latitude' => 4.2276,
                'longitude' => 73.4264,
                'map_x' => 43.00,
                'map_y' => 44.00,
                'images' => [$this->generatedImage('islands', 'horror-island')],
            ],
            [
                'name' => 'Picnic Island',
                'type' => IslandAccessService::PICNIC_ISLAND,
                'description' => 'The separate shore island for beach events, late crossings, fireside ceremonies, and quieter sea-facing gatherings.',
                'latitude' => 4.2248,
                'longitude' => 73.4268,
                'map_x' => 73.00,
                'map_y' => 58.00,
                'images' => [$this->generatedImage('islands', 'picnic-island')],
            ],
        ];

        $ids = [];

        foreach ($records as $record) {
            $island = Island::query()->updateOrCreate(
                ['name' => $record['name']],
                $record,
            );

            $ids[$record['name']] = $island->id;
        }

        return $ids;
    }

    private function seedHotels(array $owners, \Illuminate\Support\Carbon $now): array
    {
        $records = [
            [
                'name' => 'The Shining Manor',
                'user_id' => $owners['evelyn.thorne@horrorbark.test'],
                'location' => "Manor Ward · Keeper's Gate",
                'description' => 'The flagship estate of Horror Bark, defined by cold stone halls, velvet corridors, candlelit lounges, and elevated gothic hospitality.',
                'latitude' => 4.2289,
                'longitude' => 73.4257,
                'map_x' => 29.00,
                'map_y' => 20.00,
                'images' => [$this->generatedImage('hotels', 'the-shining-manor')],
            ],
            [
                'name' => 'Velvet Wake House',
                'user_id' => $owners['evelyn.thorne@horrorbark.test'],
                'location' => 'Blackwater Approach · Night Tide Dock',
                'description' => 'A harbor-facing boutique stay for late arrivals, with black-water views, lantern-lit balconies, and discreet luxury.',
                'latitude' => 4.2265,
                'longitude' => 73.4250,
                'map_x' => 21.00,
                'map_y' => 70.00,
                'images' => [$this->generatedImage('hotels', 'velvet-wake-house')],
            ],
            [
                'name' => 'Coldstone Chambers',
                'user_id' => $owners['evelyn.thorne@horrorbark.test'],
                'location' => 'Lantern Hollow · Moonfall Steps',
                'description' => 'Quiet chapel-quarter accommodations with cedar smoke, stone arches, intimate lounges, and a hushed old-world mood.',
                'latitude' => 4.2258,
                'longitude' => 73.4279,
                'map_x' => 68.00,
                'map_y' => 30.00,
                'images' => [$this->generatedImage('hotels', 'coldstone-chambers')],
            ],
        ];

        $ids = [];

        foreach ($records as $record) {
            $hotel = Hotel::query()->updateOrCreate(
                ['name' => $record['name']],
                $record,
            );

            $ids[$record['name']] = $hotel->id;
        }

        return $ids;
    }

    private function seedRooms(array $hotels): void
    {
        $records = [
            [
                'hotel_id' => $hotels['The Shining Manor'],
                'room_number' => 'SM-101 · North Tower Suite',
                'price' => 780.00,
                'status' => 'available',
                'max_occupancy' => 2,
                'amenities' => ['Moonlit bath', 'Velvet lounge', 'Private supper service'],
                'images' => [$this->generatedImage('rooms', 'shining-north-tower-suite')],
                'description' => 'A secluded tower suite above Keeper\'s Gate with moonlit stone, heavy drapery, and a formal dining nook.',
            ],
            [
                'hotel_id' => $hotels['The Shining Manor'],
                'room_number' => 'SM-204 · Velvet Gallery Room',
                'price' => 620.00,
                'status' => 'available',
                'max_occupancy' => 2,
                'amenities' => ['Gallery breakfast', 'Marble washstand', 'Lantern service'],
                'images' => [$this->generatedImage('rooms', 'shining-velvet-gallery-room')],
                'description' => 'An intimate chamber overlooking the manor gallery, designed for elegant overnight stays with silver-service touches.',
            ],
            [
                'hotel_id' => $hotels['The Shining Manor'],
                'room_number' => 'SM-310 · Midnight Conservatory Suite',
                'price' => 840.00,
                'status' => 'available',
                'max_occupancy' => 3,
                'amenities' => ['Glass conservatory nook', 'Private tea service', 'Night concierge'],
                'images' => [$this->generatedImage('rooms', 'shining-midnight-conservatory')],
                'description' => 'A premium suite with a glass-roof sitting room, rare plants, and a midnight tea setting under the moon.',
            ],
            [
                'hotel_id' => $hotels['Velvet Wake House'],
                'room_number' => 'VW-110 · Harbor View Chamber',
                'price' => 540.00,
                'status' => 'available',
                'max_occupancy' => 3,
                'amenities' => ['Dockside breakfast', 'Storm glass bar', 'Night tide balcony'],
                'images' => [$this->generatedImage('rooms', 'wake-harbor-view-chamber')],
                'description' => 'A harbor-facing chamber with panoramic dock views and a compact private bar for late-night arrivals.',
            ],
            [
                'hotel_id' => $hotels['Velvet Wake House'],
                'room_number' => 'VW-203 · Bell Tower Room',
                'price' => 585.00,
                'status' => 'available',
                'max_occupancy' => 2,
                'amenities' => ['Private writing desk', 'Sea-facing balcony', 'Late ferry valet'],
                'images' => [$this->generatedImage('rooms', 'wake-bell-tower-room')],
                'description' => 'A high room above the harbor bells, with a writing desk, balcony seating, and a view across the black tide.',
            ],
            [
                'hotel_id' => $hotels['Velvet Wake House'],
                'room_number' => 'VW-305 · Tidecaller Suite',
                'price' => 690.00,
                'status' => 'available',
                'max_occupancy' => 4,
                'amenities' => ['Corner lounge', 'Harbor soaking tub', 'Private arrival service'],
                'images' => [$this->generatedImage('rooms', 'wake-tidecaller-suite')],
                'description' => 'A larger corner suite designed for small groups, with wraparound views of the dock lanterns and tide.',
            ],
            [
                'hotel_id' => $hotels['Coldstone Chambers'],
                'room_number' => 'CC-008 · Lantern Cellar',
                'price' => 460.00,
                'status' => 'available',
                'max_occupancy' => 2,
                'amenities' => ['Cedar stove', 'Lantern alcove', 'Stone bath'],
                'images' => [$this->generatedImage('rooms', 'coldstone-lantern-cellar')],
                'description' => 'A low-lit cellar suite with rough stone textures, cedar warmth, and a tucked-away private bathing area.',
            ],
            [
                'hotel_id' => $hotels['Coldstone Chambers'],
                'room_number' => 'CC-302 · Moonfall Loft',
                'price' => 510.00,
                'status' => 'available',
                'max_occupancy' => 4,
                'amenities' => ['Loft sitting room', 'Night watch service', 'Gathering table'],
                'images' => [$this->generatedImage('rooms', 'coldstone-moonfall-loft')],
                'description' => 'A lofted family room near Lantern Hollow, balancing cozy occupancy with the property\'s dark ceremonial character.',
            ],
            [
                'hotel_id' => $hotels['Coldstone Chambers'],
                'room_number' => 'CC-214 · Chapel Eaves Room',
                'price' => 550.00,
                'status' => 'available',
                'max_occupancy' => 2,
                'amenities' => ['Window prayer nook', 'Cedar wardrobe', 'Late cocoa tray'],
                'images' => [$this->generatedImage('rooms', 'coldstone-chapel-eaves')],
                'description' => 'A serene upper-floor room with chapel roofline views and a calmer, more reflective take on the Horror Bark mood.',
            ],
        ];

        foreach ($records as $record) {
            Room::query()->updateOrCreate(
                ['room_number' => $record['room_number']],
                $record,
            );
        }
    }

    private function seedRides(array $owners, array $islands): void
    {
        $records = [
            [
                'user_id' => $owners['silas.blackwood@horrorbark.test'],
                'island_id' => $islands['Horror Island'],
                'name' => "Widow's Descent",
                'description' => 'A towering plunge ride threaded through torn velvet, black iron, and tolling bells.',
                'price' => 190.00,
                'latitude' => 4.2279,
                'longitude' => 73.4276,
                'map_x' => 38.00,
                'map_y' => 54.00,
                'images' => [$this->generatedImage('rides', 'widows-descent')],
                'max_capacity' => 28,
                'max_booking_quantity' => 4,
            ],
            [
                'user_id' => $owners['silas.blackwood@horrorbark.test'],
                'island_id' => $islands['Horror Island'],
                'name' => 'Velvet Spiral',
                'description' => 'A polished manor-side coaster that coils through violet lamps and ceremonial arches.',
                'price' => 165.00,
                'latitude' => 4.2284,
                'longitude' => 73.4262,
                'map_x' => 37.00,
                'map_y' => 27.00,
                'images' => [$this->generatedImage('rides', 'velvet-spiral')],
                'max_capacity' => 22,
                'max_booking_quantity' => 4,
            ],
            [
                'user_id' => $owners['silas.blackwood@horrorbark.test'],
                'island_id' => $islands['Horror Island'],
                'name' => 'The Ash Procession',
                'description' => 'A solemn dark ride passing through smoke, lantern arches, and chapel-like set pieces.',
                'price' => 150.00,
                'latitude' => 4.2257,
                'longitude' => 73.4282,
                'map_x' => 61.00,
                'map_y' => 33.00,
                'images' => [$this->generatedImage('rides', 'the-ash-procession')],
                'max_capacity' => 18,
                'max_booking_quantity' => 3,
            ],
        ];

        foreach ($records as $record) {
            Ride::query()->updateOrCreate(
                ['name' => $record['name']],
                $record,
            );
        }
    }

    private function seedGames(array $owners, array $islands): void
    {
        $records = [
            [
                'user_id' => $owners['jasper.crowe@horrorbark.test'],
                'island_id' => $islands['Horror Island'],
                'name' => 'Lantern Guess',
                'description' => 'A timing-and-observation game where guests choose the correct warded lantern before the flame drops.',
                'price' => 45.00,
                'latitude' => 4.2273,
                'longitude' => 73.4270,
                'map_x' => 44.00,
                'map_y' => 60.00,
                'images' => [$this->generatedImage('games', 'lantern-guess')],
                'max_capacity' => 30,
                'max_booking_quantity' => 6,
            ],
            [
                'user_id' => $owners['jasper.crowe@horrorbark.test'],
                'island_id' => $islands['Horror Island'],
                'name' => 'The Silent Wheel',
                'description' => 'A velvet-draped wheel-of-fortune attraction that feels ceremonial rather than playful.',
                'price' => 60.00,
                'latitude' => 4.2281,
                'longitude' => 73.4259,
                'map_x' => 32.00,
                'map_y' => 31.00,
                'images' => [$this->generatedImage('games', 'the-silent-wheel')],
                'max_capacity' => 24,
                'max_booking_quantity' => 4,
            ],
            [
                'user_id' => $owners['jasper.crowe@horrorbark.test'],
                'island_id' => $islands['Horror Island'],
                'name' => 'Coven Toss',
                'description' => 'A ring-toss midway game built from ashwood posts, bone-white rings, and occult carnival styling.',
                'price' => 40.00,
                'latitude' => 4.2255,
                'longitude' => 73.4276,
                'map_x' => 58.00,
                'map_y' => 39.00,
                'images' => [$this->generatedImage('games', 'coven-toss')],
                'max_capacity' => 26,
                'max_booking_quantity' => 5,
            ],
        ];

        foreach ($records as $record) {
            Game::query()->updateOrCreate(
                ['name' => $record['name']],
                $record,
            );
        }
    }

    private function seedBeachEvents(array $owners, array $islands): void
    {
        $records = [
            [
                'user_id' => $owners['ophelia.vale@horrorbark.test'],
                'island_id' => $islands['Picnic Island'],
                'name' => 'Moonlight Vigil',
                'description' => 'An after-dark shoreline gathering of lanterns, strings, and whispered vows timed to the turning tide.',
                'event_date' => CarbonImmutable::parse('2026-04-18'),
                'price' => 120.00,
                'latitude' => 4.2248,
                'longitude' => 73.4265,
                'map_x' => 76.00,
                'map_y' => 67.00,
                'images' => [$this->generatedImage('beach-events', 'moonlight-vigil')],
                'max_capacity' => 80,
                'max_booking_quantity' => 4,
            ],
            [
                'user_id' => $owners['ophelia.vale@horrorbark.test'],
                'island_id' => $islands['Picnic Island'],
                'name' => 'Velvet Bonfire',
                'description' => 'A black-sand ceremony built around a controlled bonfire, velvet seating, and salt-heavy sea air.',
                'event_date' => CarbonImmutable::parse('2026-04-25'),
                'price' => 135.00,
                'latitude' => 4.2241,
                'longitude' => 73.4284,
                'map_x' => 57.00,
                'map_y' => 16.00,
                'images' => [$this->generatedImage('beach-events', 'velvet-bonfire')],
                'max_capacity' => 60,
                'max_booking_quantity' => 4,
            ],
        ];

        foreach ($records as $record) {
            BeachEvent::query()->updateOrCreate(
                ['name' => $record['name']],
                $record,
            );
        }
    }

    private function seedFerries(array $owners, array $islands): void
    {
        $records = [
            [
                'user_id' => $owners['ophelia.vale@horrorbark.test'],
                'island_id' => $islands['Horror Island'],
                'name' => "Keeper's Passage",
                'description' => 'A formal harbor approach for manor guests, shaped around velvet seating, quiet service, and torchlit arrivals.',
                'price' => 75.00,
                'max_capacity' => 36,
                'max_booking_quantity' => 6,
                'map_x' => 19.00,
                'map_y' => 63.00,
                'images' => [$this->generatedImage('ferries', 'keepers-passage')],
            ],
            [
                'user_id' => $owners['ophelia.vale@horrorbark.test'],
                'island_id' => $islands['Picnic Island'],
                'name' => 'Night Tide Passage',
                'description' => 'A black-water crossing that reaches the outer shore under low lantern light and a heavier band of sea mist.',
                'price' => 55.00,
                'max_capacity' => 42,
                'max_booking_quantity' => 6,
                'map_x' => 58.00,
                'map_y' => 18.00,
                'images' => [$this->generatedImage('ferries', 'night-tide-passage')],
            ],
            [
                'user_id' => $owners['ophelia.vale@horrorbark.test'],
                'island_id' => $islands['Picnic Island'],
                'name' => 'Moonwake Line',
                'description' => 'The late ferry for shoreline gatherings, arriving beside pale surf, ceremonial fires, and moonlit seating.',
                'price' => 60.00,
                'max_capacity' => 40,
                'max_booking_quantity' => 6,
                'map_x' => 80.00,
                'map_y' => 67.00,
                'images' => [$this->generatedImage('ferries', 'moonwake-line')],
            ],
        ];

        foreach ($records as $record) {
            $ferry = Ferry::query()->updateOrCreate(
                ['name' => $record['name']],
                $record,
            );

            DB::table('ferry_slots')->where('ferry_id', $ferry->id)->delete();

            foreach ([9, 12, 15] as $hour) {
                DB::table('ferry_slots')->insert([
                    'ferry_id' => $ferry->id,
                    'slot_date' => now()->addDay()->toDateString(),
                    'start_time' => sprintf('%02d:00:00', $hour),
                    'end_time' => sprintf('%02d:00:00', $hour + 1),
                    'capacity' => $record['max_capacity'],
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function generatedImage(string $collection, string $slug): string
    {
        $candidate = "{$collection}/gallery/{$slug}-01.png";

        if (file_exists(storage_path("app/public/{$candidate}"))) {
            return $candidate;
        }

        return HorrorGeneratedMediaCatalog::path($collection, $slug);
    }
}
