@props([
    'links' => [],
    'copyright' => null,
])

@php
    $legalLinks = !empty($links) ? $links : [
        ['label' => 'Privacy Policy', 'href' => '#'],
        ['label' => 'Terms of Service', 'href' => '#'],
    ];
@endphp

<footer class="relative mt-auto border-t border-primary-light/20 bg-background-dark pt-20 pb-10 text-primary-light">
    <div class="pointer-events-none absolute inset-0 bg-primary-light/[0.02]"></div>

    <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-16 grid grid-cols-1 gap-12 md:grid-cols-4">
            <div>
                <a href="{{ route('home') }}" class="text-metallic mb-6 block font-display text-2xl font-bold uppercase tracking-[0.2em]">
                    Horror-Bark
                </a>
                <p class="font-serif text-base leading-relaxed opacity-80">
                    The destination for gothic luxury and island thrills. Enter with a reservation and leave with a story.
                </p>
            </div>

            <div>
                <h4 class="mb-6 font-display text-lg tracking-wider text-moonlight">Explore</h4>
                <ul class="space-y-4 font-serif text-sm">
                    <li><a href="{{ route('hotels.index') }}" class="inline-block transition duration-300 hover:translate-x-1 hover:text-white">The Manor</a></li>
                    <li><a href="{{ route('themepark.index') }}" class="inline-block transition duration-300 hover:translate-x-1 hover:text-white">Shadow Park</a></li>
                    <li><a href="{{ route('beach-events.index') }}" class="inline-block transition duration-300 hover:translate-x-1 hover:text-white">Moonlight Events</a></li>
                    <li><a href="{{ url('/about') }}" class="inline-block transition duration-300 hover:translate-x-1 hover:text-white">Our Lore</a></li>
                </ul>
            </div>

            <div>
                <h4 class="mb-6 font-display text-lg tracking-wider text-moonlight">Support</h4>
                <ul class="space-y-4 font-serif text-sm">
                    <li><a href="{{ route('contacts.create') }}" class="inline-block transition duration-300 hover:translate-x-1 hover:text-white">Contact Keeper</a></li>
                    <li><a href="{{ route('ferries.index') }}" class="inline-block transition duration-300 hover:translate-x-1 hover:text-white">Ferry Schedule</a></li>
                    <li><a href="{{ route('bookings.index') }}" class="inline-block transition duration-300 hover:translate-x-1 hover:text-white">My Bookings</a></li>
                    <li><a href="{{ route('profile.edit') }}" class="inline-block transition duration-300 hover:translate-x-1 hover:text-white">My Profile</a></li>
                </ul>
            </div>

            <div>
                <h4 class="mb-6 font-display text-lg tracking-wider text-moonlight">Join the Coven</h4>
                <form class="flex flex-col space-y-4" action="#" method="POST">
                    <input
                        type="email"
                        placeholder="Enter your email"
                        class="w-full border border-primary-light/30 bg-background-dark px-4 py-3 font-serif text-base text-moonlight placeholder:text-primary-light/60 focus:border-primary-light focus:outline-none"
                    />
                    <button type="button" class="border border-primary-light/40 bg-primary-dark px-4 py-3 font-display text-sm uppercase tracking-[0.2em] text-moonlight transition duration-300 hover:bg-primary">
                        Summon Us
                    </button>
                </form>
            </div>
        </div>

        <div class="flex flex-col items-center justify-between border-t border-primary-light/20 pt-8 font-serif text-sm opacity-75 md:flex-row">
            <p>{{ $copyright ?: '© ' . date('Y') . ' Horror-Bark. All rights reserved.' }}</p>

            <div class="mt-4 flex space-x-8 md:mt-0">
                @foreach ($legalLinks as $link)
                    <a href="{{ $link['href'] }}" class="transition-colors hover:text-moonlight">{{ $link['label'] }}</a>
                @endforeach
            </div>
        </div>
    </div>
</footer>
