<?php

namespace App\Providers\Filament;

use App\Filament\Dashboard;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Game\Widgets\GameBookingsByDayChart;
use App\Filament\Game\Widgets\GameNext7DaysOverview;
use App\Filament\Game\Widgets\GameQuickActionsWidget;
use App\Filament\Game\Widgets\GameStatsOverview;

class GamePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('game')
            ->path('game')
            ->colors([
                'primary' => Color::Blue, // You can pick a different color if you want
            ])
            ->brandName('Horror Bark · Game Portal')
            ->login()
            ->viteTheme('resources/css/filament/panels/theme.css')
            ->discoverResources(in: app_path('Filament/Game/Resources'), for: 'App\\Filament\\Game\\Resources')
            ->discoverPages(in: app_path('Filament/Game/Pages'), for: 'App\\Filament\\Game\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->navigationItems([
                NavigationItem::make('Reports')
                    ->url(fn (): string => route('operator-reports.index', ['domain' => 'game']))
                    ->icon('heroicon-o-chart-bar')
                    ->group('Insights')
                    ->sort(1)
                    ->isActiveWhen(fn (): bool => request()->routeIs('operator-reports.*') && request()->route('domain') === 'game'),
            ])
            ->discoverWidgets(in: app_path('Filament/Game/Widgets'), for: 'App\\Filament\\Game\\Widgets')
            ->widgets([
                GameNext7DaysOverview::class,
                GameQuickActionsWidget::class,
                GameStatsOverview::class,
                GameBookingsByDayChart::class,
                Widgets\AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
