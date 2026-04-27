<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IslandAtlasTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_renders_the_fictional_island_atlas_preview(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->get(route('home'));

        $response->assertOk()
            ->assertSee('Island Atlas')
            ->assertSee('Blackwater Approach')
            ->assertSee('Manor Ward')
            ->assertSee('Shadow Park')
            ->assertSee('Pale Moon Strand')
            ->assertSee('Moonlight Vigil');
    }

    public function test_themepark_page_focuses_on_the_registry_without_the_island_map(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->get(route('themepark.index'));

        $response->assertOk()
            ->assertSee('Shadow Park Registry')
            ->assertSee('Active Attractions')
            ->assertSee('Velvet Spiral')
            ->assertSee('The Silent Wheel')
            ->assertDontSee('Island Atlas');
    }
}
