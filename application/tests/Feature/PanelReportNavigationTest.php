<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PanelReportNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_report_link_is_available_from_sidebar(): void
    {
        $this->assertPanelReportNavigation(
            role: 'admin',
            panel: '/admin',
            labels: ['Insights', 'Reports'],
            urls: [route('admin-reports.index')],
        );
    }

    public function test_hotel_report_link_is_available_from_sidebar(): void
    {
        $this->assertPanelReportNavigation(
            role: 'hotel_manager',
            panel: '/hotel',
            labels: ['Insights', 'Reports'],
            urls: [route('operator-reports.index', ['domain' => 'hotel'])],
        );
    }

    public function test_ferry_report_links_are_available_from_sidebar(): void
    {
        $this->assertPanelReportNavigation(
            role: 'ferry_manager',
            panel: '/ferry',
            labels: ['Insights', 'Booking Reports', 'Passenger Reports'],
            urls: [
                route('operator-reports.index', ['domain' => 'ferry']),
                route('ferry-reports.index'),
            ],
        );
    }

    public function test_ride_report_link_is_available_from_sidebar(): void
    {
        $this->assertPanelReportNavigation(
            role: 'ride_manager',
            panel: '/ride',
            labels: ['Insights', 'Reports'],
            urls: [route('operator-reports.index', ['domain' => 'ride'])],
        );
    }

    public function test_game_report_link_is_available_from_sidebar(): void
    {
        $this->assertPanelReportNavigation(
            role: 'game_manager',
            panel: '/game',
            labels: ['Insights', 'Reports'],
            urls: [route('operator-reports.index', ['domain' => 'game'])],
        );
    }

    private function assertPanelReportNavigation(string $role, string $panel, array $labels, array $urls): void
    {
        Role::findOrCreate($role, 'web');

        $user = User::factory()->create();
        $user->assignRole($role);

        $response = $this->actingAs($user)->get($panel);
        $response->assertOk();

        foreach ($labels as $label) {
            $response->assertSeeText($label);
        }

        foreach ($urls as $url) {
            $response->assertSee(parse_url($url, PHP_URL_PATH), false);
        }
    }
}
