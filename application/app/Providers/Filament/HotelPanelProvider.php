<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
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
use App\Filament\Hotel\Widgets\HotelBookingsByDayChart;
use App\Filament\Hotel\Widgets\HotelNext7DaysOverview;
use App\Filament\Hotel\Widgets\HotelQuickActionsWidget;
use App\Filament\Hotel\Widgets\HotelStatsOverview;



class HotelPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
{
    return $panel
        ->id('hotel')
        ->path('hotel')
        ->colors([
            'primary' => Color::Rose,
        ])
        ->brandName('Horror Bark · Hotel Portal')
        ->login()
        ->viteTheme('resources/css/filament/panels/theme.css')
        ->discoverResources(in: app_path('Filament/Hotel/Resources'), for: 'App\\Filament\\Hotel\\Resources')
        ->discoverPages(in: app_path('Filament/Hotel/Pages'), for: 'App\\Filament\\Hotel\\Pages')
        ->pages([
            Pages\Dashboard::class,
        ])
        ->navigationItems([
            NavigationItem::make('Reports')
                ->url(fn (): string => route('operator-reports.index', ['domain' => 'hotel']))
                ->icon('heroicon-o-chart-bar')
                ->group('Insights')
                ->sort(1)
                ->isActiveWhen(fn (): bool => request()->routeIs('operator-reports.*') && request()->route('domain') === 'hotel'),
        ])
        ->discoverWidgets(in: app_path('Filament/Hotel/Widgets'), for: 'App\\Filament\\Hotel\\Widgets')
        ->widgets([
            HotelNext7DaysOverview::class,
            HotelQuickActionsWidget::class,
            HotelStatsOverview::class,
            HotelBookingsByDayChart::class,
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
