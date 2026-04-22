@extends('layouts.app')

@section('title', 'About Us - Horror-Bark Theme Park')

@section('content')
<main class="space-y-14 pb-8">
    <section class="overflow-hidden rounded-[2rem] border border-primary-light/15 bg-[radial-gradient(circle_at_top,_rgba(156,107,255,0.16),_transparent_58%),linear-gradient(135deg,_rgba(12,11,22,0.98),_rgba(18,13,28,0.93))] shadow-[0_30px_80px_rgba(0,0,0,0.35)]">
        <div class="grid lg:grid-cols-[1.05fr,0.95fr]">
            <div class="space-y-6 px-6 py-10 sm:px-10 lg:py-14">
                <p class="theme-kicker">Our Lore</p>
                <h1 class="font-display text-5xl italic leading-none text-moonlight sm:text-6xl">A haunted island built for beautiful dread.</h1>
                <p class="readable-copy max-w-2xl">
                    Horror-Bark was imagined as a destination where moonlit hospitality, theatrical fear, and island spectacle could belong to the same world.
                    Guests arrive for the atmosphere first, then stay for the details: the crossings, the rooms, the rides, the rituals, and the stories stitched through every shoreline.
                </p>
                <p class="readable-copy max-w-2xl">
                    It is not a park that begins at the gate. It begins at first sight of the harbor, in the lanterns reflecting off black water,
                    in the slow recognition that every path, tower, queue, and drawing room has been tuned to the same emotional register.
                </p>

                <div class="grid gap-3 sm:grid-cols-3">
                    <div class="theme-detail-card">
                        <p class="theme-label">Built Around</p>
                        <p class="theme-detail-value !text-lg">Hotels, ferries, rides, games, and beach events</p>
                    </div>
                    <div class="theme-detail-card">
                        <p class="theme-label">Mood</p>
                        <p class="theme-detail-value !text-lg">Gothic luxury with maritime unease</p>
                    </div>
                    <div class="theme-detail-card">
                        <p class="theme-label">Promise</p>
                        <p class="theme-detail-value !text-lg">Memorable fear without breaking the spell</p>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    <x-ui.button :href="route('hotels.index')" variant="primary">Explore the Stays</x-ui.button>
                    <x-ui.button :href="route('themepark.index')" variant="ghost">View the Attractions</x-ui.button>
                </div>
            </div>

            <div class="min-h-[24rem]">
                <img
                    src="{{ asset('storage/about/gallery/about-harbor-hero-01.png') }}"
                    alt="Moonlit harbor view of Horror-Bark"
                    class="h-full w-full object-cover"
                >
            </div>
        </div>
    </section>

    <section class="grid gap-8 lg:grid-cols-[1.05fr,0.95fr]">
        <div class="space-y-5">
            <x-ui.section-heading title="The Island Was Never Meant To Feel Ordinary" size="lg" />
            <p class="readable-copy">
                The park began as a response to safe, forgettable entertainment. Instead of flattening every experience into the same bright corridor,
                Horror-Bark treats the entire island as a single continuous set piece. The crossing should feel like an arrival. The hotel should feel like prologue.
                The shoreline should feel ceremonial. Even the queue should feel like a story beat.
            </p>
            <p class="readable-copy">
                That philosophy shaped everything that followed. Architectural silhouettes were pushed toward old-world grandeur, the waterline was preserved as part of the show,
                and the guest journey was designed to oscillate between elegance and unease rather than jump straight to noise.
            </p>
            <p class="readable-copy">
                The result is an island built around emotional pacing. There are places where the world tightens and places where it breathes. There are routes meant to
                disorient slightly before they resolve into spectacle. There are rooms that feel calmer than the shore outside them, and attractions that only work because
                the path leading toward them has already done half the storytelling.
            </p>
        </div>

        <x-ui.surface class="space-y-4">
            <p class="theme-kicker">How The World Is Structured</p>
            <div class="space-y-3">
                <div class="theme-detail-card">
                    <p class="theme-label">Arrival</p>
                    <p class="theme-detail-value !text-sm">The ferry crossing establishes the mood before the island is fully revealed.</p>
                </div>
                <div class="theme-detail-card">
                    <p class="theme-label">Stay</p>
                    <p class="theme-detail-value !text-sm">Lodging acts as part of the atmosphere, not a separate utility layer.</p>
                </div>
                <div class="theme-detail-card">
                    <p class="theme-label">Play</p>
                    <p class="theme-detail-value !text-sm">Rides, games, and events are curated as different expressions of the same mythology.</p>
                </div>
                <div class="theme-detail-card">
                    <p class="theme-label">Afterglow</p>
                    <p class="theme-detail-value !text-sm">Guests should leave with specific scenes in memory, not just a list of activities completed.</p>
                </div>
            </div>
        </x-ui.surface>
    </section>

    <section class="grid gap-8 lg:grid-cols-[0.92fr,1.08fr] lg:items-center">
        <div class="overflow-hidden rounded-[1.75rem] border border-primary-light/15 shadow-cold-shadow">
            <img
                src="{{ asset('storage/about/gallery/about-grand-foyer-01.png') }}"
                alt="Grand candlelit foyer at Horror-Bark"
                class="h-full w-full object-cover"
            >
        </div>

        <div class="space-y-5">
            <x-ui.section-heading title="Designed Like A Set, Operated Like A Destination" size="lg" />
            <p class="readable-copy">
                Every public-facing space is meant to feel curated. Dark stone, lantern brass, velvet, sea fog, and antique glass are not decoration layered on afterwards;
                they are the visual grammar of the place. Horror-Bark works best when the guest can read the island instantly, even before they understand the story in words.
            </p>
            <p class="readable-copy">
                That restraint matters. Horror-Bark is not meant to feel cluttered with references or overloaded with explanation. The island works when every surface looks like it belongs,
                every pool of light feels deliberate, and every piece of ornament suggests history without having to announce it. The goal is immersion through coherence rather than excess.
            </p>
            <div class="grid gap-3 sm:grid-cols-2">
                <div class="theme-detail-card">
                    <p class="theme-label">Visual Language</p>
                    <p class="theme-detail-value !text-sm">Moon-silver highlights, amber practical light, wet black surfaces, and restrained ornament.</p>
                </div>
                <div class="theme-detail-card">
                    <p class="theme-label">Operational Principle</p>
                    <p class="theme-detail-value !text-sm">Guests should always know where to go next without feeling pushed out of the mood.</p>
                </div>
                <div class="theme-detail-card sm:col-span-2">
                    <p class="theme-label">What That Means In Practice</p>
                    <p class="theme-detail-value !text-sm">Readable signage, coherent booking flows, consistent theming, and enough narrative restraint that the world still feels believable.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="space-y-6">
        <x-ui.section-heading title="Meet the Minds Behind the Mayhem" size="lg" />
        <p class="readable-copy max-w-4xl">
            Horror-Bark is sustained by a small leadership core that treats atmosphere, logistics, and guest flow as equal parts of the same craft.
            Their work is less about shocking people moment-to-moment and more about making the island feel internally complete.
        </p>
        <p class="readable-copy max-w-4xl">
            Even in absence, their disciplines are legible. Each one leaves behind a different kind of trace: the architecture of an idea, the evidence of staging,
            the concealed logic inside a mechanism. The island is full of signatures, but almost none of them are literal.
        </p>

        <x-about.team-grid :members="[
            [
                'name' => 'Evelyn Thorne',
                'role' => 'Visionary Founder',
                'image' => asset('storage/about/gallery/evelyn-thorne-01.png'),
                'focus' => 'Narrative direction, guest mood, and the island-wide tone of the brand',
                'bio' => 'Evelyn anchors the long-form worldbuilding behind Horror-Bark. Her hand is felt most strongly in the island’s pacing: what is revealed immediately, what is withheld, and how hospitality is used to make the fear feel more intimate instead of less.',
            ],
            [
                'name' => 'Silas Blackwood',
                'role' => 'Creative Director',
                'image' => asset('storage/about/gallery/silas-blackwood-01.png'),
                'focus' => 'Show design, visual standards, staging, and atmosphere continuity',
                'bio' => 'Silas shapes the ornamental language of the island, from drapery and lantern placement to how props, railings, and threshold spaces hold the same cinematic identity even when guests are moving quickly through them.',
            ],
            [
                'name' => 'Jasper Crowe',
                'role' => 'Lead Engineer',
                'image' => asset('storage/about/gallery/jasper-crowe-01.png'),
                'focus' => 'Ride systems, reliability, and keeping the machinery invisible to the guest',
                'bio' => 'Jasper handles the technical backbone that allows the island to feel effortless. His mandate is simple: the illusion should hold even when the operation underneath it is complex, timed, redundant, and constantly under pressure.',
            ],
        ]" />
    </section>

    <section class="space-y-6">
        <x-ui.section-heading title="What We Refuse To Compromise" size="lg" />

        <div class="grid gap-4 md:grid-cols-3">
            <x-ui.surface class="space-y-3">
                <p class="theme-kicker">Atmosphere First</p>
                <p class="readable-copy">If a feature improves throughput but damages the mood beyond repair, it is redesigned until both can coexist.</p>
            </x-ui.surface>
            <x-ui.surface class="space-y-3">
                <p class="theme-kicker">Readable Operations</p>
                <p class="readable-copy">Guests should be able to move through bookings, crossings, and event access without friction breaking the illusion.</p>
            </x-ui.surface>
            <x-ui.surface class="space-y-3">
                <p class="theme-kicker">Safe Spectacle</p>
                <p class="readable-copy">The experience is built to feel unnerving, not careless. Safety is part of the design discipline, not a note added at the end.</p>
            </x-ui.surface>
        </div>
    </section>

    <section class="rounded-[1.75rem] border border-primary-light/15 bg-[linear-gradient(180deg,rgba(14,11,20,0.94),rgba(9,7,14,0.96))] px-6 py-8 shadow-cold-shadow sm:px-8">
        <div class="space-y-5">
            <x-ui.section-heading title="Connect with the Unseen" size="lg" />
            <p class="readable-copy max-w-4xl">
                Questions about the island, the stay, the crossings, or the events can be sent directly to the team.
                If you are planning a visit, the fastest path is still to explore the catalog and then reach out with the details that need a human answer.
            </p>
            <p class="readable-copy max-w-4xl">
                Whether you are trying to understand the world before you arrive or simply decide which part of the island should be experienced first,
                the team can point you toward the stay, route, or event that best matches the mood you want from the visit.
            </p>
            <x-about.contact-info
                email="info@horror-bark.com"
                phone="+960 999999"
                location="4.2260552262693,73.426287174225"
            />

            <div class="flex flex-wrap gap-3 pt-2">
                <x-ui.button :href="route('contacts.create')" variant="primary">Send an Enquiry</x-ui.button>
                <x-ui.button :href="route('bookings.index')" variant="ghost">Open My Portal</x-ui.button>
            </div>
        </div>
    </section>
</main>
@endsection
