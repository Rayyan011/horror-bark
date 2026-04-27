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
use App\Filament\Ferry\Widgets\FerryBookingsByDayChart;
use App\Filament\Ferry\Widgets\FerryBookingsByIslandChart;
use App\Filament\Ferry\Widgets\FerryLoadFactorByHourChart;
use App\Filament\Ferry\Widgets\FerryNext7DaysOverview;
use App\Filament\Ferry\Widgets\FerryQuickActionsWidget;
use App\Filament\Ferry\Widgets\FerryStatsOverview;
use App\Filament\Ferry\Widgets\FerryTodayDeparturesTable;

class FerryPanelProvider extends PanelProvider
{
   public function panel(Panel $panel): Panel
{
    return $panel
        ->id('ferry')
        ->path('ferry')
        ->colors([
            'primary' => Color::Amber,
        ])
        ->brandName('Horror Bark · Ferry Portal')
        ->login()
        ->viteTheme('resources/css/filament/panels/theme.css')
        ->discoverResources(in: app_path('Filament/Ferry/Resources'), for: 'App\\Filament\\Ferry\\Resources')
        ->discoverPages(in: app_path('Filament/Ferry/Pages'), for: 'App\\Filament\\Ferry\\Pages')
        ->pages([
            Dashboard::class,
        ])
        ->navigationItems([
            NavigationItem::make('Booking Reports')
                ->url(fn (): string => route('operator-reports.index', ['domain' => 'ferry']))
                ->icon('heroicon-o-chart-bar')
                ->group('Insights')
                ->sort(1)
                ->isActiveWhen(fn (): bool => request()->routeIs('operator-reports.*') && request()->route('domain') === 'ferry'),
            NavigationItem::make('Passenger Reports')
                ->url(fn (): string => route('ferry-reports.index'))
                ->icon('heroicon-o-document-chart-bar')
                ->group('Insights')
                ->sort(2)
                ->isActiveWhen(fn (): bool => request()->routeIs('ferry-reports.*')),
        ])
        ->discoverWidgets(in: app_path('Filament/Ferry/Widgets'), for: 'App\\Filament\\Ferry\\Widgets')
        ->widgets([
            FerryNext7DaysOverview::class,
            FerryQuickActionsWidget::class,
            FerryStatsOverview::class,
            FerryBookingsByDayChart::class,
            FerryBookingsByIslandChart::class,
            FerryLoadFactorByHourChart::class,
            FerryTodayDeparturesTable::class,
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
