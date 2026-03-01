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

            <nav class="hidden md:flex md:flex-1 md:items-center">
                <ul class="flex w-full items-center justify-between">
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

                    <li>
                        <div
                            x-data="{ light: false, msg: false }"
                            class="relative flex items-center"
                            @click.outside="msg = false"
                        >
                            <button
                                @click="light = true; setTimeout(() => { light = false; msg = true; setTimeout(() => msg = false, 3000) }, 350)"
                                class="group flex items-center gap-1.5 border-b border-transparent pb-1 font-serif text-xs uppercase tracking-[0.2em] text-primary-light transition-all duration-300 hover:border-primary-light/60 hover:text-white"
                                title="Toggle light mode"
                            >
                                <span x-show="!light" class="material-symbols-outlined text-base">nightlight</span>
                                <span x-show="light" class="material-symbols-outlined text-base text-amber-300">light_mode</span>
                                <span x-text="light ? 'Light' : 'Night'"></span>
                            </button>

                            {{-- Full-page flash overlay --}}
                            <div
                                x-show="light"
                                x-cloak
                                x-transition:enter="transition duration-150"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                x-transition:leave="transition duration-200"
                                x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0"
                                class="fixed inset-0 z-[9998] bg-white pointer-events-none"
                            ></div>

                            {{-- "It's better in the dark" message --}}
                            <div
                                x-show="msg"
                                x-cloak
                                x-transition:enter="transition duration-300"
                                x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
                                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                x-transition:leave="transition duration-200"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute top-8 right-0 z-[9999] w-56 rounded border border-primary-light/30 bg-background-dark px-4 py-3 shadow-[0_8px_30px_rgba(0,0,0,0.9)] text-center"
                            >
                                <p class="font-serif text-sm italic leading-relaxed text-primary-light">
                                    ...It's better in the dark.
                                </p>
                            </div>
                        </div>
                    </li>
                </ul>
            </nav>

            <details class="group md:hidden">
                <summary class="flex cursor-pointer list-none items-center text-primary-light transition-colors hover:text-white">
                    <span class="material-symbols-outlined text-3xl">menu</span>
                </summary>
                <div class="absolute left-0 right-0 top-24 border-b border-primary-light/20 bg-background-dark/95 px-4 py-5 shadow-[0_18px_40px_rgba(0,0,0,0.85)] backdrop-blur-md sm:px-6">
                    <ul class="space-y-3">
                        <li>
                            <div
                                x-data="{ light: false, msg: false }"
                                class="relative"
                                @click.outside="msg = false"
                            >
                                <button
                                    @click="light = true; setTimeout(() => { light = false; msg = true; setTimeout(() => msg = false, 3000) }, 350)"
                                    class="flex items-center gap-2 font-serif text-xs uppercase tracking-[0.2em] text-primary-light transition-colors hover:text-white"
                                >
                                    <span x-show="!light" class="material-symbols-outlined text-base">nightlight</span>
                                    <span x-show="light" class="material-symbols-outlined text-base text-amber-300">light_mode</span>
                                    <span x-text="light ? 'Light Mode' : 'Night Mode'"></span>
                                </button>
                                <p
                                    x-show="msg"
                                    x-cloak
                                    x-transition
                                    class="mt-1 font-serif text-xs italic text-primary-light"
                                >...It's better in the dark.</p>
                            </div>
                        </li>

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
