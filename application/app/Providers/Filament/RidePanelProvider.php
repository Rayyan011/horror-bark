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
use App\Filament\Ride\Widgets\RideBookingsByDayChart;
use App\Filament\Ride\Widgets\RideCapacityUtilizationBySlotChart;
use App\Filament\Ride\Widgets\RideNext7DaysOverview;
use App\Filament\Ride\Widgets\RideQuickActionsWidget;
use App\Filament\Ride\Widgets\RideStatsOverview;
use App\Filament\Ride\Widgets\RideTopRidesTable;


class RidePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('ride')
            ->path('ride')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->brandName('Horror Bark · Ride Portal')
            ->login()
            ->viteTheme('resources/css/filament/panels/theme.css')
            ->discoverResources(in: app_path('Filament/Ride/Resources'), for: 'App\\Filament\\Ride\\Resources')
            ->discoverPages(in: app_path('Filament/Ride/Pages'), for: 'App\\Filament\\Ride\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->navigationItems([
                NavigationItem::make('Reports')
                    ->url(fn (): string => route('operator-reports.index', ['domain' => 'ride']))
                    ->icon('heroicon-o-chart-bar')
                    ->group('Insights')
                    ->sort(1)
                    ->isActiveWhen(fn (): bool => request()->routeIs('operator-reports.*') && request()->route('domain') === 'ride'),
            ])
            ->discoverWidgets(in: app_path('Filament/Ride/Widgets'), for: 'App\\Filament\\Ride\\Widgets')
            ->widgets([
                RideNext7DaysOverview::class,
                RideQuickActionsWidget::class,
                RideStatsOverview::class,
                RideBookingsByDayChart::class,
                RideCapacityUtilizationBySlotChart::class,
                RideTopRidesTable::class,
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
