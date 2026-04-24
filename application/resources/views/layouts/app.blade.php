@php
    $navItems = [
        ['label' => 'Home', 'href' => route('home'), 'active' => request()->routeIs('home')],
        ['label' => 'Hotels', 'href' => route('hotels.index'), 'active' => request()->routeIs('hotels.*')],
        ['label' => 'Ferry Tickets', 'href' => route('ferries.index'), 'active' => request()->routeIs('ferries.*')],
        ['label' => 'Theme Park', 'href' => route('themepark.index'), 'active' => request()->routeIs('themepark.*')],
        ['label' => 'Beach Events', 'href' => route('beach-events.index'), 'active' => request()->routeIs('beach-events.*')],
        ['label' => 'Contact', 'href' => route('contacts.create'), 'active' => request()->routeIs('contacts.*')],
        ['label' => 'About', 'href' => url('/about'), 'active' => request()->is('about')],
    ];

    $authNavItems = [
        ['label' => 'Portal', 'href' => route('portal'), 'active' => request()->routeIs('portal')],
        ['label' => 'My Bookings', 'href' => route('bookings.index'), 'active' => request()->routeIs('bookings.*')],
        ['label' => 'My Profile', 'href' => route('profile.edit'), 'active' => request()->routeIs('profile.*')],
        ['label' => 'Logout', 'href' => route('logout'), 'method' => 'POST'],
    ];

    $guestNavItems = [
        ['label' => 'Login', 'href' => route('login'), 'active' => request()->routeIs('login')],
        ['label' => 'Register', 'href' => route('register'), 'active' => request()->routeIs('register')],
    ];

    $heroRoutes = ['home', 'login', 'register', 'password.request', 'password.reset'];
    $showHero = request()->routeIs($heroRoutes);
@endphp

<x-ui.app-shell :title="trim($__env->yieldContent('title')) ?: 'Horror-Bark Theme Park'">
    <x-slot:header>
        <x-ui.site-header
            brand="Horror-Bark"
            :nav-items="$navItems"
            :auth-nav-items="$authNavItems"
            :guest-nav-items="$guestNavItems"
        />
    </x-slot:header>

    @if ($showHero)
        <x-slot:hero>
            @hasSection('hero')
                @yield('hero')
            @else
                <x-ui.hero-banner
                    :image="\App\Support\HorrorBarkThemeAssets::homeHero()"
                    title="Welcome to Horror-Bark"
                    subtitle="Reserve your stay, cross the channel, and explore every corner of the island."
                    height="h-[62vh]"
                />
            @endif
        </x-slot:hero>
    @endif

    @yield('content')

    <x-slot:footer>
        <x-ui.site-footer :links="[
            ['label' => 'Privacy Policy', 'href' => '#'],
            ['label' => 'Terms of Service', 'href' => '#'],
        ]" />
    </x-slot:footer>
</x-ui.app-shell>
