<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HeroVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_hero_is_visible_on_landing_and_auth_pages(): void
    {
        $urls = [
            route('home'),
            route('login'),
            route('register'),
            route('password.request'),
            route('password.reset', ['token' => 'smoke-token', 'email' => 'guest@example.com']),
        ];

        foreach ($urls as $url) {
            $response = $this->get($url);

            $response->assertOk();
            $response->assertSeeText($url === route('home') ? 'Horror-Bark Arrival Ledger' : 'Under the Pale Moon');
        }
    }

    public function test_hero_is_hidden_on_non_landing_pages(): void
    {
        $urls = [
            route('ferries.index'),
            route('themepark.index'),
            route('about'),
        ];

        foreach ($urls as $url) {
            $response = $this->get($url);

            $response->assertOk();
            $response->assertDontSeeText('Under the Pale Moon');
        }
    }

    public function test_home_midnight_protocol_copy_is_rendered(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSeeText('One path through the island.');
        $response->assertSeeText('The landing page now mirrors the real customer flow: offers first, districts second, then the broader night registry.');
    }
}
