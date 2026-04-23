@props([
    'zones' => collect(),
    'locations' => collect(),
    'featured' => collect(),
    'height' => 'min-h-[34rem]',
    'compact' => false,
    'title' => 'Horror-Bark Isle',
    'subtitle' => 'Navigate the fog-laden districts, moonlit shores, and ritual approaches before the tide shifts again.',
    'initialFilter' => 'all',
    'initialSelection' => null,
])

@php
    $zones = collect($zones)->values();
    $locations = collect($locations)->values();
    $featured = collect($featured)->values();
    $initialSelection = $initialSelection ?: data_get($locations->first(), 'slug');

    $filters = [
        ['value' => 'all', 'label' => 'All'],
        ['value' => 'hotel', 'label' => 'Hotels'],
        ['value' => 'ride', 'label' => 'Rides'],
        ['value' => 'game', 'label' => 'Games'],
        ['value' => 'beach_event', 'label' => 'Beach Events'],
        ['value' => 'ferry', 'label' => 'Ferries'],
    ];

    $categoryMeta = [
        'hotel' => ['marker' => 'atlas-marker-hotel', 'icon' => 'bed'],
        'ride' => ['marker' => 'atlas-marker-ride', 'icon' => 'rocket_launch'],
        'game' => ['marker' => 'atlas-marker-game', 'icon' => 'casino'],
        'beach_event' => ['marker' => 'atlas-marker-event', 'icon' => 'local_fire_department'],
        'ferry' => ['marker' => 'atlas-marker-ferry', 'icon' => 'directions_boat'],
    ];
@endphp

@once
    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                window.horrorAtlasComponent = (config) => ({
                    filter: config.initialFilter || 'all',
                    selected: config.initialSelection || (config.locations[0]?.slug ?? null),
                    locations: config.locations,
                    init() {
                        this.ensureSelection();
                    },
                    setFilter(filter) {
                        this.filter = filter;
                        this.ensureSelection();
                    },
                    ensureSelection() {
                        const visible = this.visibleLocations();

                        if (! visible.length) {
                            this.selected = null;
                            return;
                        }

                        if (! visible.some((location) => location.slug === this.selected)) {
                            this.selected = visible[0].slug;
                        }
                    },
                    isVisible(category) {
                        return this.filter === 'all' || this.filter === category;
                    },
                    visibleLocations() {
                        return this.filter === 'all'
                            ? this.locations
                            : this.locations.filter((location) => location.category === this.filter);
                    },
                    currentSelection() {
                        return this.locations.find((location) => location.slug === this.selected) ?? this.visibleLocations()[0] ?? null;
                    },
                    select(slug) {
                        this.selected = slug;
                    },
                });
            });
        </script>
    @endpush
@endonce

<section
    x-data="horrorAtlasComponent({
        locations: @js($locations),
        initialFilter: @js($initialFilter),
        initialSelection: @js($initialSelection),
    })"
    class="atlas-shell"
>
    <div class="mb-8 flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
        <div class="space-y-3">
            <p class="atlas-kicker">Island Cartography</p>
            <h2 class="atlas-title">{{ $title }}</h2>
            <p class="max-w-3xl font-serif text-base leading-relaxed text-primary-light/80">
                {{ $subtitle }}
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            @foreach ($filters as $filter)
                <button
                    type="button"
                    x-on:click="setFilter('{{ $filter['value'] }}')"
                    x-bind:class="filter === '{{ $filter['value'] }}' ? 'atlas-filter is-active' : 'atlas-filter'"
                    class="{{ $filter['value'] === $initialFilter ? 'atlas-filter is-active' : 'atlas-filter' }}"
                >
                    {{ $filter['label'] }}
                </button>
            @endforeach
        </div>
    </div>

    <div class="grid gap-6 {{ $compact ? '2xl:grid-cols-[minmax(0,1fr)_20rem]' : '2xl:grid-cols-[18rem_minmax(0,1fr)_22rem]' }}">
        @unless ($compact)
            <aside class="atlas-rail">
                <div class="atlas-rail-card">
                    <p class="atlas-kicker text-[0.68rem]">Featured Destinations</p>
                    <div class="mt-6 space-y-4">
                        @foreach ($featured as $item)
                            <button
                                type="button"
                                x-on:click="select('{{ $item['slug'] }}')"
                                x-show="isVisible('{{ $item['category'] }}')"
                                class="atlas-rail-item"
                            >
                                <span class="atlas-rail-name">{{ $item['name'] }}</span>
                                <span class="atlas-rail-meta">{{ $item['zoneName'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="atlas-rail-card">
                    <p class="atlas-kicker text-[0.68rem]">Legend</p>
                    <div class="mt-5 space-y-3 text-sm text-primary-light/80">
                        @foreach ($filters as $filter)
                            @continue($filter['value'] === 'all')
                            <div class="flex items-center gap-3">
                                <span class="atlas-legend-icon {{ $categoryMeta[$filter['value']]['marker'] }}">
                                    <span class="material-symbols-outlined">{{ $categoryMeta[$filter['value']]['icon'] }}</span>
                                </span>
                                <span>{{ $filter['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </aside>
        @endunless

        <div class="atlas-stage-wrapper {{ $height }}">
            <div class="atlas-stage">
                <div class="atlas-grid"></div>
                <div class="atlas-water-glow atlas-water-glow-left"></div>
                <div class="atlas-water-glow atlas-water-glow-right"></div>
                <div class="atlas-isle atlas-isle-main"></div>
                <div class="atlas-isle atlas-isle-north"></div>
                <div class="atlas-isle atlas-isle-shore"></div>
                <div class="atlas-fog atlas-fog-top"></div>
                <div class="atlas-fog atlas-fog-bottom"></div>
                <div class="atlas-compass">
                    <span>N</span>
                </div>

                @foreach ($zones as $zone)
                    <div
                        class="atlas-zone {{ str_contains($zone['type'] ?? '', 'Picnic') ? 'atlas-zone--shore' : 'atlas-zone--ward' }}"
                        style="left: {{ $zone['x'] }}%; top: {{ $zone['y'] }}%;"
                    >
                        <span class="atlas-zone-halo"></span>
                        <span class="atlas-zone-label">{{ $zone['name'] }}</span>
                    </div>
                @endforeach

                @foreach ($locations as $location)
                    @php($meta = $categoryMeta[$location['category']] ?? ['marker' => 'atlas-marker-hotel', 'icon' => 'bed'])
                    <button
                        type="button"
                        x-show="isVisible('{{ $location['category'] }}')"
                        x-on:click="select('{{ $location['slug'] }}')"
                        x-bind:class="selected === '{{ $location['slug'] }}' ? 'atlas-marker-button {{ $meta['marker'] }} is-selected' : 'atlas-marker-button {{ $meta['marker'] }}'"
                        class="atlas-marker-button {{ $meta['marker'] }}"
                        style="left: {{ $location['x'] }}%; top: {{ $location['y'] }}%;"
                    >
                        <span class="atlas-marker-ping"></span>
                        <span class="material-symbols-outlined">{{ $location['icon'] }}</span>
                        <span class="atlas-marker-tooltip">{{ $location['name'] }}</span>
                    </button>
                @endforeach

                <div class="atlas-legend-panel">
                    <p class="atlas-kicker text-[0.68rem]">Map Legend</p>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        @foreach ($filters as $filter)
                            @continue($filter['value'] === 'all')
                            <div class="flex items-center gap-3 text-xs uppercase tracking-[0.18em] text-primary-light/80">
                                <span class="atlas-legend-icon {{ $categoryMeta[$filter['value']]['marker'] }}">
                                    <span class="material-symbols-outlined">{{ $categoryMeta[$filter['value']]['icon'] }}</span>
                                </span>
                                <span>{{ $filter['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <aside class="atlas-detail-card">
            <template x-if="currentSelection()">
                <div>
                    <div class="atlas-detail-media">
                        <img
                            x-bind:src="currentSelection().image"
                            x-bind:alt="currentSelection().name"
                            class="h-full w-full object-cover transition duration-[1800ms] hover:scale-110"
                        >
                    </div>

                    <div class="space-y-5 p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="atlas-kicker text-[0.68rem]" x-text="currentSelection().eyebrow"></p>
                                <h3 class="mt-2 font-display text-3xl italic leading-none text-moonlight" x-text="currentSelection().name"></h3>
                            </div>
                            <span class="material-symbols-outlined text-primary-light">verified</span>
                        </div>

                        <div class="space-y-3 text-sm leading-relaxed text-primary-light/80">
                            <p x-text="currentSelection().description"></p>
                            <div class="grid gap-2 text-xs uppercase tracking-[0.18em] text-primary-light/70">
                                <p>
                                    <span x-text="currentSelection().zoneLabel"></span>
                                    <span class="mx-2 text-primary-light/30">•</span>
                                    <span x-text="currentSelection().zoneName"></span>
                                </p>
                                <p x-text="currentSelection().stat"></p>
                                <p x-text="currentSelection().secondary"></p>
                            </div>
                        </div>

                        <div class="flex flex-col gap-3 sm:flex-row">
                            <a
                                x-bind:href="currentSelection().href"
                                class="inline-flex flex-1 items-center justify-center border border-primary-light/25 px-4 py-3 text-xs uppercase tracking-[0.2em] text-primary-light transition duration-300 hover:border-primary-light hover:text-moonlight"
                            >
                                <span x-text="currentSelection().ctaLabel"></span>
                            </a>
                            <button
                                type="button"
                                class="inline-flex flex-1 items-center justify-center border border-[#8b5cf6]/35 bg-[#8b5cf6]/12 px-4 py-3 text-xs uppercase tracking-[0.2em] text-moonlight transition duration-300 hover:border-[#8b5cf6] hover:bg-[#8b5cf6]/18"
                                x-on:click="setFilter(currentSelection().category)"
                            >
                                Show Category
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </aside>
    </div>
</section>
