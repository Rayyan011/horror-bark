@props([
    'brand' => 'Horror-Bark',
    'navItems' => [],
    'authNavItems' => [],
    'guestNavItems' => [],
])

<header class="bg-black text-white p-4 shadow-md">
    <div class="container mx-auto flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <a href="{{ route('home') }}">
            <h1 class="text-3xl font-bold horror-font">{{ $brand }}</h1>
        </a>

        <nav>
            <ul class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm md:text-base">
                @foreach ($navItems as $item)
                    <li>
                        <x-ui.nav-item
                            :label="$item['label']"
                            :href="$item['href']"
                            :active="$item['active'] ?? false"
                        />
                    </li>
                @endforeach

                @auth
                    @foreach ($authNavItems as $item)
                        <li>
                            @if (($item['method'] ?? 'GET') === 'GET')
                                <x-ui.nav-item
                                    :label="$item['label']"
                                    :href="$item['href']"
                                    :active="$item['active'] ?? false"
                                />
                            @else
                                <form method="POST" action="{{ $item['href'] }}">
                                    @csrf
                                    @if (strtoupper($item['method']) !== 'POST')
                                        @method($item['method'])
                                    @endif
                                    <button type="submit" class="text-gray-200 hover:text-gray-400 transition-colors">
                                        {{ $item['label'] }}
                                    </button>
                                </form>
                            @endif
                        </li>
                    @endforeach
                @endauth

                @guest
                    @foreach ($guestNavItems as $item)
                        <li>
                            <x-ui.nav-item
                                :label="$item['label']"
                                :href="$item['href']"
                                :active="$item['active'] ?? false"
                            />
                        </li>
                    @endforeach
                @endguest
            </ul>
        </nav>
    </div>
</header>
