@props([
    'brand' => 'Horror-Bark',
    'navItems' => [],
    'authNavItems' => [],
    'guestNavItems' => [],
])

@php
    $primaryNavItems = array_slice($navItems, 0, 5);
    $secondaryNavItems = array_slice($navItems, 5);
@endphp

<header class="relative sticky top-0 z-50 border-b border-primary-light/20 bg-background-dark/95 shadow-[0_16px_30px_rgba(0,0,0,0.7)] backdrop-blur-md">
    <div class="mx-auto max-w-[112rem] px-4 sm:px-6 lg:px-8">
        <div class="flex min-h-24 items-center justify-between gap-6 py-4 xl:gap-10">
            <a href="{{ route('home') }}" class="shrink-0">
                <h1 class="text-metallic font-display text-3xl font-bold uppercase tracking-normal lg:text-4xl">{{ $brand }}</h1>
            </a>

            <nav class="hidden flex-1 items-center justify-end xl:flex">
                <div class="flex flex-col items-end gap-4">
                    <ul class="flex items-center justify-end gap-x-8 gap-y-2">
                    @foreach ($primaryNavItems as $item)
                        <li>
                            <x-ui.nav-item
                                :label="$item['label']"
                                :href="$item['href']"
                                :active="$item['active'] ?? false"
                            />
                        </li>
                    @endforeach
                    </ul>

                    <div class="flex items-center justify-end gap-8">
                        @if (count($secondaryNavItems))
                            <ul class="flex items-center justify-end gap-x-7">
                                @foreach ($secondaryNavItems as $item)
                                    <li>
                                        <x-ui.nav-item
                                            :label="$item['label']"
                                            :href="$item['href']"
                                            :active="$item['active'] ?? false"
                                        />
                                    </li>
                                @endforeach
                            </ul>
                            <span class="h-4 w-px bg-primary-light/20" aria-hidden="true"></span>
                        @endif

                        <ul class="flex items-center justify-end gap-x-7">
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
                                        <button type="submit" class="border-b border-transparent pb-1 font-serif text-xs uppercase tracking-normal text-primary-light transition-all duration-300 hover:border-primary-light/60 hover:text-white">
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
                </div>
            </nav>

            <details class="group xl:hidden">
                <summary class="flex cursor-pointer list-none items-center text-primary-light transition-colors hover:text-white">
                    <span class="material-symbols-outlined text-3xl">menu</span>
                </summary>
                <div class="absolute left-0 right-0 top-full border-b border-primary-light/20 bg-background-dark/95 px-4 py-5 shadow-[0_18px_40px_rgba(0,0,0,0.85)] backdrop-blur-md sm:px-6">
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
                                            <button type="submit" class="font-serif text-xs uppercase tracking-normal text-primary-light transition-colors hover:text-white">
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
