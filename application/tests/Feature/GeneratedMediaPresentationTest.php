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

    public function test_hotel_detail_page_shows_generated_hotel_and_room_images(): void
    {
        $this->seed(DatabaseSeeder::class);
        $hotel = Hotel::query()->where('name', 'The Shining Manor')->firstOrFail();

        $response = $this->get(route('hotels.show', $hotel));

        $response->assertOk()
            ->assertSee('/generated-media/hotels/the-shining-manor.svg')
            ->assertSee('/generated-media/rooms/shining-north-tower-suite.svg');
    }
}
