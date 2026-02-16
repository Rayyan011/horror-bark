@props([
    'brand' => 'Horror-Bark',
    'navItems' => [],
    'authNavItems' => [],
    'guestNavItems' => [],
])

<header class="relative sticky top-0 z-50 border-b border-primary-light/20 bg-background-dark/95 shadow-[0_16px_30px_rgba(0,0,0,0.7)] backdrop-blur-md">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-24 items-center justify-between gap-8">
            <a href="{{ route('home') }}" class="shrink-0">
                <h1 class="text-metallic font-display text-2xl font-bold uppercase tracking-[0.22em] md:text-3xl">{{ $brand }}</h1>
            </a>

            <nav class="hidden md:flex md:flex-1 md:items-center md:justify-between">
                <ul class="flex flex-wrap items-center gap-x-6 gap-y-2">
                    @foreach ($navItems as $item)
                        <li>
                            <x-ui.nav-item
                                :label="$item['label']"
                                :href="$item['href']"
                                :active="$item['active'] ?? false"
                            />
                        </li>
                    @endforeach
                </ul>

                <ul class="flex flex-wrap items-center gap-x-6 gap-y-2">
                    @auth
                        @foreach ($authNavItems as $item)
                            <li>
                                @if (strtoupper($item['method'] ?? 'GET') === 'GET')
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
                                        <button type="submit" class="border-b border-transparent pb-1 font-serif text-xs uppercase tracking-[0.2em] text-primary-light transition-all duration-300 hover:border-primary-light/60 hover:text-white">
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

            <details class="group md:hidden">
                <summary class="flex cursor-pointer list-none items-center text-primary-light transition-colors hover:text-white">
                    <span class="material-symbols-outlined text-3xl">menu</span>
                </summary>
                <div class="absolute left-0 right-0 top-24 border-b border-primary-light/20 bg-background-dark/95 px-4 py-5 shadow-[0_18px_40px_rgba(0,0,0,0.85)] backdrop-blur-md sm:px-6">
                    <ul class="space-y-3">
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
                                    @if (strtoupper($item['method'] ?? 'GET') === 'GET')
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
                                            <button type="submit" class="font-serif text-xs uppercase tracking-[0.2em] text-primary-light transition-colors hover:text-white">
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
                </div>
            </details>
        </div>
    </div>
</header>
