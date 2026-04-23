<?php

namespace Tests\Feature;

use App\Models\Hotel;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GeneratedMediaPresentationTest extends TestCase
{
    use RefreshDatabase;

    public function test_generated_media_route_returns_svg_artwork(): void
    {
        $response = $this->get('/generated-media/hotels/the-shining-manor.svg');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'image/svg+xml');
        $response->assertSee('<svg', false);
        $response->assertSee('The Shining Manor');
    }

    public function test_hotel_detail_page_prefers_storage_backed_hotel_and_room_images(): void
    {
        $this->seed(DatabaseSeeder::class);
        $hotel = Hotel::query()->where('name', 'The Shining Manor')->firstOrFail();

        $response = $this->get(route('hotels.show', $hotel));

        $response->assertOk()
            ->assertSee('storage/hotels/gallery/the-shining-manor-01.png')
            ->assertSee('storage/rooms/gallery/shining-north-tower-suite-01.png');
    }

    public function test_ferry_listing_prefers_storage_backed_ferry_images(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->get(route('ferries.index'));

        $response->assertOk()
            ->assertSee('storage/ferries/gallery/keepers-passage-01.png')
            ->assertSee('storage/ferries/gallery/night-tide-passage-01.png')
            ->assertSee('storage/ferries/gallery/moonwake-line-01.png');
    }

    public function test_generated_media_route_supports_new_stitch_room_variants(): void
    {
        foreach ([
            '/generated-media/rooms/shining-midnight-conservatory.svg',
            '/generated-media/rooms/wake-tidecaller-suite.svg',
            '/generated-media/rooms/coldstone-chapel-eaves.svg',
        ] as $path) {
            $response = $this->get($path);

            $response->assertOk();
            $response->assertHeader('Content-Type', 'image/svg+xml');
            $response->assertSee('<svg', false);
        }
    }
}
