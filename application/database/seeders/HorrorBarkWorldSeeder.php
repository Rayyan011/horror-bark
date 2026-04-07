<?php

namespace Database\Seeders;

use App\Services\IslandAccessService;
use App\Support\HorrorGeneratedMediaCatalog;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class HorrorBarkWorldSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $owners = $this->seedOwners($now);

        $this->resetWorldTables();

        $islands = $this->seedIslands($now);
        $hotels = $this->seedHotels($owners, $now);

        $this->seedRooms($hotels, $now);
        $this->seedRides($islands, $owners, $now);
        $this->seedGames($islands, $owners, $now);
        $this->seedBeachEvents($islands, $owners, $now);
        $this->seedFerries($islands, $owners, $now);
        $this->seedPromotions($now);
    }

    private function seedOwners(Carbon $now): array
    {
        $owners = [
            'evelyn.thorne@horrorbark.test' => 'Evelyn Thorne',
            'silas.blackwood@horrorbark.test' => 'Silas Blackwood',
            'jasper.crowe@horrorbark.test' => 'Jasper Crowe',
            'ophelia.vale@horrorbark.test' => 'Ophelia Vale',
            'mara.voss@horrorbark.test' => 'Mara Voss',
        ];

        foreach ($owners as $email => $name) {
            DB::table('users')->updateOrInsert(
                ['email' => $email],
                [
                    'name' => $name,
                    'email_verified_at' => $now,
                    'password' => Hash::make('test@admin.com'),
                    'remember_token' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            );
        }

        return collect(array_keys($owners))
            ->mapWithKeys(fn (string $email) => [$email => (int) DB::table('users')->where('email', $email)->value('id')])
            ->all();
    }

    private function resetWorldTables(): void
    {
        Schema::disableForeignKeyConstraints();

        foreach ([
            'audit_logs',
            'invoices',
            'beach_event_bookings',
            'ferry_bookings',
            'game_bookings',
            'hotel_bookings',
            'ride_bookings',
            'ferry_slots',
            'ferries',
            'beach_events',
            'games',
            'rides',
            'rooms',
            'hotels',
            'promotions',
            'islands',
        ] as $table) {
            DB::table($table)->delete();
        }

        Schema::enableForeignKeyConstraints();
    }

    private function seedIslands(Carbon $now): array
    {
        $records = [
            [
                'name' => 'Manor Ward',
                'type' => IslandAccessService::HORROR_ISLAND,
                'description' => 'The velvet-lit heart of Horror-Bark, where stone promenades, watchful gates, and the grand manor set the tone for every arrival.',
                'latitude' => 4.2288,
                'longitude' => 73.4258,
                'map_x' => 31.00,
                'map_y' => 22.00,
                'images' => $this->json([$this->generatedImage('islands', 'manor-ward')]),
            ],
            [
                'name' => 'Shadow Park',
                'type' => IslandAccessService::HORROR_ISLAND,
                'description' => 'A grand amusement district of twisted iron rides, carnival lamps, and polished dread beneath the pale moon.',
                'latitude' => 4.2276,
                'longitude' => 73.4274,
                'map_x' => 33.00,
                'map_y' => 58.00,
                'images' => $this->json([$this->generatedImage('islands', 'shadow-park')]),
            ],
            [
                'name' => 'Lantern Hollow',
                'type' => IslandAccessService::HORROR_ISLAND,
                'description' => 'A cedar-dark hollow of chapel bells, ash paths, and warding lanterns that never fully warm the fog.',
                'latitude' => 4.2259,
                'longitude' => 73.4280,
                'map_x' => 69.00,
                'map_y' => 29.00,
                'images' => $this->json([$this->generatedImage('islands', 'lantern-hollow')]),
            ],
            [
                'name' => 'Blackwater Approach',
                'type' => IslandAccessService::HORROR_ISLAND,
                'description' => 'The harbor edge of Horror-Bark, where ferries arrive through black tide and the first bells carry over the water.',
                'latitude' => 4.2267,
                'longitude' => 73.4249,
                'map_x' => 18.00,
                'map_y' => 74.00,
                'images' => $this->json([$this->generatedImage('islands', 'blackwater-approach')]),
            ],
            [
                'name' => 'Pale Moon Strand',
                'type' => IslandAccessService::PICNIC_ISLAND,
                'description' => 'A moon-washed shore of velvet seating, ceremonial fires, and midnight gatherings shaped around the surf.',
                'latitude' => 4.2249,
                'longitude' => 73.4267,
                'map_x' => 78.00,
                'map_y' => 72.00,
                'images' => $this->json([$this->generatedImage('islands', 'pale-moon-strand')]),
            ],
            [
                'name' => 'Saltveil Beach',
                'type' => IslandAccessService::PICNIC_ISLAND,
                'description' => 'Ash-gray sand and salt-heavy mist frame the island’s most elegant beach rituals and late-night performances.',
                'latitude' => 4.2243,
                'longitude' => 73.4286,
                'map_x' => 56.00,
                'map_y' => 20.00,
                'images' => $this->json([$this->generatedImage('islands', 'saltveil-beach')]),
            ],
            [
                'name' => 'Coven Quay',
                'type' => IslandAccessService::PICNIC_ISLAND,
                'description' => 'A lantern-lined quay for arrivals, departures, and shoreline vigils where ferrymen whisper the night’s schedule.',
                'latitude' => 4.2256,
                'longitude' => 73.4252,
                'map_x' => 67.00,
                'map_y' => 50.00,
                'images' => $this->json([$this->generatedImage('islands', 'coven-quay')]),
            ],
            [
                'name' => 'Blackwater Shore',
                'type' => IslandAccessService::PICNIC_ISLAND,
                'description' => 'The deepest curve of the outer shore, known for tide ceremonies, black surf, and music carried in from the quay.',
                'latitude' => 4.2248,
                'longitude' => 73.4242,
                'map_x' => 83.00,
                'map_y' => 45.00,
                'images' => $this->json([$this->generatedImage('islands', 'blackwater-shore')]),
            ],
        ];

        $ids = [];

        foreach ($records as $record) {
            $ids[$record['name']] = (int) DB::table('islands')->insertGetId([
                ...$record,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        return $ids;
    }

    private function seedHotels(array $owners, Carbon $now): array
    {
        $records = [
            [
                'name' => 'The Shining Manor',
                'user_id' => $owners['evelyn.thorne@horrorbark.test'],
                'location' => "Manor Ward · Keeper's Gate",
                'description' => 'The signature estate of Horror-Bark: cold stone halls, velvet corridors, and suites arranged for guests who prefer elegance with their dread.',
                'latitude' => 4.2289,
                'longitude' => 73.4257,
                'map_x' => 29.00,
                'map_y' => 20.00,
                'images' => $this->json([$this->generatedImage('hotels', 'the-shining-manor')]),
            ],
            [
                'name' => 'Velvet Wake House',
                'user_id' => $owners['evelyn.thorne@horrorbark.test'],
                'location' => 'Blackwater Approach · Night Tide Dock',
                'description' => 'A harbor residence for late arrivals and discreet departures, with candlelit lounges overlooking the black tide.',
                'latitude' => 4.2265,
                'longitude' => 73.4250,
                'map_x' => 21.00,
                'map_y' => 70.00,
                'images' => $this->json([$this->generatedImage('hotels', 'velvet-wake-house')]),
            ],
            [
                'name' => 'Coldstone Chambers',
                'user_id' => $owners['evelyn.thorne@horrorbark.test'],
                'location' => 'Lantern Hollow · Moonfall Steps',
                'description' => 'Quiet chambers tucked into the chapel quarter, where lantern smoke, cedar ash, and moonlit velvet settle into every room.',
                'latitude' => 4.2258,
                'longitude' => 73.4279,
                'map_x' => 68.00,
                'map_y' => 30.00,
                'images' => $this->json([$this->generatedImage('hotels', 'coldstone-chambers')]),
            ],
        ];

        $ids = [];

        foreach ($records as $record) {
            $ids[$record['name']] = (int) DB::table('hotels')->insertGetId([
                ...$record,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        return $ids;
    }

    private function seedRooms(array $hotels, Carbon $now): void
    {
        $rooms = [
            [
                'hotel_id' => $hotels['The Shining Manor'],
                'room_number' => 'SM-101 · North Tower Suite',
                'price' => 780,
                'status' => 'available',
                'max_occupancy' => 2,
                'amenities' => $this->json(['Moonlit bath', 'Velvet lounge', 'Private supper service']),
                'images' => $this->json([$this->generatedImage('rooms', 'shining-north-tower-suite')]),
                'description' => 'A quiet suite above Keeper\'s Gate with cold stone walls, violet drapery, and bells that ring only after midnight.',
            ],
            [
                'hotel_id' => $hotels['The Shining Manor'],
                'room_number' => 'SM-204 · Velvet Gallery Room',
                'price' => 620,
                'status' => 'available',
                'max_occupancy' => 2,
                'amenities' => $this->json(['Gallery breakfast', 'Marble washstand', 'Lantern service']),
                'images' => $this->json([$this->generatedImage('rooms', 'shining-velvet-gallery-room')]),
                'description' => 'An intimate room overlooking the manor gallery, furnished for guests who prefer silver service and long shadows.',
            ],
            [
                'hotel_id' => $hotels['Velvet Wake House'],
                'room_number' => 'VW-110 · Harbor View Chamber',
                'price' => 540,
                'status' => 'available',
                'max_occupancy' => 3,
                'amenities' => $this->json(['Dockside breakfast', 'Storm glass bar', 'Night tide balcony']),
                'images' => $this->json([$this->generatedImage('rooms', 'wake-harbor-view-chamber')]),
                'description' => 'A harbor-facing chamber with wide windows over the quay and a bar stocked for arrivals after the final bell.',
            ],
            [
                'hotel_id' => $hotels['Velvet Wake House'],
                'room_number' => 'VW-203 · Bell Tower Room',
                'price' => 585,
                'status' => 'available',
                'max_occupancy' => 2,
                'amenities' => $this->json(['Private writing desk', 'Sea-facing balcony', 'Late ferry valet']),
                'images' => $this->json([$this->generatedImage('rooms', 'wake-bell-tower-room')]),
                'description' => 'A high room above the dock lanterns, favored by guests who arrive with luggage light and secrets heavy.',
            ],
            [
                'hotel_id' => $hotels['Coldstone Chambers'],
                'room_number' => 'CC-008 · Lantern Cellar',
                'price' => 460,
                'status' => 'available',
                'max_occupancy' => 2,
                'amenities' => $this->json(['Cedar stove', 'Lantern alcove', 'Stone bath']),
                'images' => $this->json([$this->generatedImage('rooms', 'coldstone-lantern-cellar')]),
                'description' => 'A low-lit cellar suite beneath the chapel quarter, warmed by cedar and arranged for a quiet stay off the main promenade.',
            ],
            [
                'hotel_id' => $hotels['Coldstone Chambers'],
                'room_number' => 'CC-302 · Moonfall Loft',
                'price' => 510,
                'status' => 'available',
                'max_occupancy' => 4,
                'amenities' => $this->json(['Loft sitting room', 'Night watch service', 'Gathering table']),
                'images' => $this->json([$this->generatedImage('rooms', 'coldstone-moonfall-loft')]),
                'description' => 'A lofted family chamber at the edge of Lantern Hollow, ideal for parties bound for rites, rides, and the midnight shore.',
            ],
        ];

        foreach ($rooms as $room) {
            DB::table('rooms')->insert([
                ...$room,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    private function seedRides(array $islands, array $owners, Carbon $now): void
    {
        $rides = [
            [
                'user_id' => $owners['silas.blackwood@horrorbark.test'],
                'island_id' => $islands['Shadow Park'],
                'name' => "Widow's Descent",
                'description' => 'A towering plunge through torn velvet, cathedral ironwork, and bells that answer only the fall.',
                'price' => 190,
                'latitude' => 4.2279,
                'longitude' => 73.4276,
                'map_x' => 38.00,
                'map_y' => 54.00,
                'images' => $this->json([$this->generatedImage('rides', 'widows-descent')]),
                'max_capacity' => 28,
                'max_booking_quantity' => 4,
            ],
            [
                'user_id' => $owners['silas.blackwood@horrorbark.test'],
                'island_id' => $islands['Manor Ward'],
                'name' => 'Velvet Spiral',
                'description' => 'A manor-side corkscrew of polished steel and violet lamps, built to turn anticipation into ceremony.',
                'price' => 165,
                'latitude' => 4.2284,
                'longitude' => 73.4262,
                'map_x' => 37.00,
                'map_y' => 27.00,
                'images' => $this->json([$this->generatedImage('rides', 'velvet-spiral')]),
                'max_capacity' => 22,
                'max_booking_quantity' => 4,
            ],
            [
                'user_id' => $owners['silas.blackwood@horrorbark.test'],
                'island_id' => $islands['Lantern Hollow'],
                'name' => 'The Ash Procession',
                'description' => 'A solemn track ride threading chapel smoke, cedar embers, and the slow procession of lantern-bearing figures.',
                'price' => 150,
                'latitude' => 4.2257,
                'longitude' => 73.4282,
                'map_x' => 61.00,
                'map_y' => 33.00,
                'images' => $this->json([$this->generatedImage('rides', 'the-ash-procession')]),
                'max_capacity' => 18,
                'max_booking_quantity' => 3,
            ],
            [
                'user_id' => $owners['silas.blackwood@horrorbark.test'],
                'island_id' => $islands['Blackwater Approach'],
                'name' => 'Nocturne Drop',
                'description' => 'A harbor-wall freefall that flashes the black sea beneath your feet before lifting you back into the fog.',
                'price' => 210,
                'latitude' => 4.2264,
                'longitude' => 73.4248,
                'map_x' => 25.00,
                'map_y' => 63.00,
                'images' => $this->json([$this->generatedImage('rides', 'nocturne-drop')]),
                'max_capacity' => 20,
                'max_booking_quantity' => 4,
            ],
        ];

        foreach ($rides as $ride) {
            DB::table('rides')->insert([
                ...$ride,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    private function seedGames(array $islands, array $owners, Carbon $now): void
    {
        $games = [
            [
                'user_id' => $owners['jasper.crowe@horrorbark.test'],
                'island_id' => $islands['Shadow Park'],
                'name' => 'Lantern Guess',
                'description' => 'Read the warding sigils, choose the proper lantern, and finish before the flame goes cold.',
                'price' => 45,
                'latitude' => 4.2273,
                'longitude' => 73.4270,
                'map_x' => 44.00,
                'map_y' => 60.00,
                'images' => $this->json([$this->generatedImage('games', 'lantern-guess')]),
                'max_capacity' => 30,
                'max_booking_quantity' => 6,
            ],
            [
                'user_id' => $owners['jasper.crowe@horrorbark.test'],
                'island_id' => $islands['Manor Ward'],
                'name' => 'The Silent Wheel',
                'description' => 'A velvet wheel of fortune that turns without music and stops with unnerving precision.',
                'price' => 60,
                'latitude' => 4.2281,
                'longitude' => 73.4259,
                'map_x' => 32.00,
                'map_y' => 31.00,
                'images' => $this->json([$this->generatedImage('games', 'the-silent-wheel')]),
                'max_capacity' => 24,
                'max_booking_quantity' => 4,
            ],
            [
                'user_id' => $owners['jasper.crowe@horrorbark.test'],
                'island_id' => $islands['Lantern Hollow'],
                'name' => 'Coven Toss',
                'description' => 'Bone-white rings, ashwood posts, and prizes awarded under the watch of the hollow lanterns.',
                'price' => 40,
                'latitude' => 4.2255,
                'longitude' => 73.4276,
                'map_x' => 58.00,
                'map_y' => 39.00,
                'images' => $this->json([$this->generatedImage('games', 'coven-toss')]),
                'max_capacity' => 26,
                'max_booking_quantity' => 5,
            ],
            [
                'user_id' => $owners['jasper.crowe@horrorbark.test'],
                'island_id' => $islands['Blackwater Approach'],
                'name' => 'Midnight Draw',
                'description' => 'A dockside table game of sealed cards, tide wagers, and prizes chosen by the harbor keeper.',
                'price' => 55,
                'latitude' => 4.2268,
                'longitude' => 73.4254,
                'map_x' => 22.00,
                'map_y' => 67.00,
                'images' => $this->json([$this->generatedImage('games', 'midnight-draw')]),
                'max_capacity' => 18,
                'max_booking_quantity' => 3,
            ],
        ];

        foreach ($games as $game) {
            DB::table('games')->insert([
                ...$game,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    private function seedBeachEvents(array $islands, array $owners, Carbon $now): void
    {
        $beachEvents = [
            [
                'user_id' => $owners['ophelia.vale@horrorbark.test'],
                'island_id' => $islands['Pale Moon Strand'],
                'name' => 'Moonlight Vigil',
                'description' => 'An after-dark gathering of lanterns, strings, and whispered vows timed to the inward pull of the tide.',
                'event_date' => Carbon::today()->addDays(4),
                'price' => 120,
                'max_capacity' => 80,
                'max_booking_quantity' => 4,
                'latitude' => 4.2248,
                'longitude' => 73.4265,
                'map_x' => 76.00,
                'map_y' => 67.00,
                'images' => $this->json([$this->generatedImage('beach-events', 'moonlight-vigil')]),
            ],
            [
                'user_id' => $owners['ophelia.vale@horrorbark.test'],
                'island_id' => $islands['Saltveil Beach'],
                'name' => 'Velvet Bonfire',
                'description' => 'Black sand, velvet seating, and a ceremonial bonfire fed with aromatic cedar and sea salt.',
                'event_date' => Carbon::today()->addDays(11),
                'price' => 135,
                'max_capacity' => 60,
                'max_booking_quantity' => 4,
                'latitude' => 4.2241,
                'longitude' => 73.4284,
                'map_x' => 57.00,
                'map_y' => 16.00,
                'images' => $this->json([$this->generatedImage('beach-events', 'velvet-bonfire')]),
            ],
            [
                'user_id' => $owners['ophelia.vale@horrorbark.test'],
                'island_id' => $islands['Coven Quay'],
                'name' => 'Lantern Wake',
                'description' => 'A midnight send-off at the quay with floating lights, low choir notes, and ferries slipping in and out of the mist.',
                'event_date' => Carbon::today()->addDays(18),
                'price' => 95,
                'max_capacity' => 70,
                'max_booking_quantity' => 5,
                'latitude' => 4.2257,
                'longitude' => 73.4254,
                'map_x' => 69.00,
                'map_y' => 46.00,
                'images' => $this->json([$this->generatedImage('beach-events', 'lantern-wake')]),
            ],
            [
                'user_id' => $owners['ophelia.vale@horrorbark.test'],
                'island_id' => $islands['Blackwater Shore'],
                'name' => 'The Pale Tide Gathering',
                'description' => 'A shoreline supper and moonlit performance staged where the surf turns black and the music carries farther than it should.',
                'event_date' => Carbon::today()->addDays(25),
                'price' => 145,
                'max_capacity' => 90,
                'max_booking_quantity' => 4,
                'latitude' => 4.2247,
                'longitude' => 73.4241,
                'map_x' => 84.00,
                'map_y' => 41.00,
                'images' => $this->json([$this->generatedImage('beach-events', 'the-pale-tide-gathering')]),
            ],
        ];

        foreach ($beachEvents as $beachEvent) {
            DB::table('beach_events')->insert([
                ...$beachEvent,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    private function seedFerries(array $islands, array $owners, Carbon $now): void
    {
        $ferries = [
            [
                'name' => "Keeper's Passage",
                'description' => 'The formal crossing from the mainland gate to Manor Ward, favored by guests with trunks, velvet cases, and evening reservations.',
                'island_id' => $islands['Manor Ward'],
                'price' => 75,
                'max_capacity' => 36,
                'max_booking_quantity' => 6,
                'map_x' => 18.00,
                'map_y' => 62.00,
            ],
            [
                'name' => 'Night Tide Passage',
                'description' => 'A late crossing that cuts through black water toward Coven Quay while the lantern masts trade signals with the shore.',
                'island_id' => $islands['Coven Quay'],
                'price' => 55,
                'max_capacity' => 42,
                'max_booking_quantity' => 6,
                'map_x' => 65.00,
                'map_y' => 53.00,
            ],
            [
                'name' => 'Moonwake Line',
                'description' => 'The preferred route for guests bound to the midnight beaches, with open decks and a quiet final approach through pale surf.',
                'island_id' => $islands['Pale Moon Strand'],
                'price' => 60,
                'max_capacity' => 40,
                'max_booking_quantity' => 6,
                'map_x' => 79.00,
                'map_y' => 62.00,
            ],
        ];

        foreach ($ferries as $ferry) {
            $ferryId = (int) DB::table('ferries')->insertGetId([
                'user_id' => $owners['mara.voss@horrorbark.test'],
                ...$ferry,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ([9, 12, 15] as $hour) {
                DB::table('ferry_slots')->insert([
                    'ferry_id' => $ferryId,
                    'slot_date' => Carbon::today()->addDay()->toDateString(),
                    'start_time' => sprintf('%02d:00:00', $hour),
                    'end_time' => sprintf('%02d:00:00', $hour + 1),
                    'capacity' => $ferry['max_capacity'],
                    'status' => 'active',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    private function seedPromotions(Carbon $now): void
    {
        $promotions = [
            [
                'title' => 'Manor & Midway Arrangement',
                'description' => 'Reserve a chamber at The Shining Manor and secure a favored rate for rides within Shadow Park before the pale moon rises.',
                'discount_percentage' => 15,
                'cta_label' => 'View Chambers',
                'cta_url' => '/hotels',
                'image_path' => $this->generatedImage('promotions', 'manor-midway-arrangement'),
                'starts_at' => $now->copy()->subDay(),
                'ends_at' => $now->copy()->addWeeks(2),
                'is_active' => true,
            ],
            [
                'title' => 'Passage Under The Pale Moon',
                'description' => 'Travel outward on the Moonwake Line and return with preferred ferry pricing for the midnight shore circuits.',
                'discount_percentage' => 10,
                'cta_label' => 'Book Ferry',
                'cta_url' => '/ferries',
                'image_path' => $this->generatedImage('promotions', 'passage-under-the-pale-moon'),
                'starts_at' => $now->copy()->subDay(),
                'ends_at' => $now->copy()->addWeeks(3),
                'is_active' => true,
            ],
            [
                'title' => 'Moonlit Shore Invitation',
                'description' => 'Select gatherings on the outer strand now include reduced admission for guests arriving before the second bell.',
                'discount_percentage' => 12,
                'cta_label' => 'Observe Events',
                'cta_url' => '/beach-events',
                'image_path' => $this->generatedImage('promotions', 'moonlit-shore-invitation'),
                'starts_at' => $now->copy()->subDay(),
                'ends_at' => $now->copy()->addWeeks(4),
                'is_active' => true,
            ],
        ];

        foreach ($promotions as $promotion) {
            DB::table('promotions')->insert([
                ...$promotion,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    private function json(array $value): string
    {
        return json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    private function generatedImage(string $collection, string $slug): string
    {
        return HorrorGeneratedMediaCatalog::path($collection, $slug);
    }
}
