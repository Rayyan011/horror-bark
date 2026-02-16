@props([
    'title' => 'Other Haunts',
    'subtitle' => 'Quiet corners and midnight comforts between the screams.',
    'cards' => collect(),
    'autoplayMs' => 6500,
])

@php
    $cards = collect($cards)->values();
@endphp

<section class="py-20">
    <x-ui.section-heading
        :title="$title"
        :subtitle="$subtitle"
        size="md"
        align="center"
        class="mb-14"
    />

    @if ($cards->isEmpty())
        <x-ui.empty-state
            title="No haunts available tonight"
            description="Return later as new rides, games, and beach events surface from the fog."
        />
    @else
        <div
            x-data="{
                total: {{ $cards->count() }},
                page: 0,
                pages: 1,
                visibleCount: 4,
                autoplayMs: {{ (int) $autoplayMs }},
                autoplay: null,
                init() {
                    this.recalculate();
                    window.addEventListener('resize', () => this.recalculate());
                    this.startAutoplay();
                },
                recalculate() {
                    const width = window.innerWidth;
                    this.visibleCount = width >= 1024 ? 4 : (width >= 768 ? 2 : 1);
                    this.pages = Math.max(1, Math.ceil(this.total / this.visibleCount));

                    if (this.page >= this.pages) {
                        this.page = 0;
                    }
                },
                startAutoplay() {
                    this.stopAutoplay();

                    if (this.pages <= 1) {
                        return;
                    }

                    this.autoplay = setInterval(() => this.next(), this.autoplayMs);
                },
                stopAutoplay() {
                    if (this.autoplay) {
                        clearInterval(this.autoplay);
                        this.autoplay = null;
                    }
                },
                next() {
                    this.page = (this.page + 1) % this.pages;
                },
                prev() {
                    this.page = (this.page - 1 + this.pages) % this.pages;
                },
                goTo(targetPage) {
                    this.page = targetPage;
                },
                isVisible(index) {
                    const start = this.page * this.visibleCount;
                    const end = start + this.visibleCount;

                    return index >= start && index < end;
                },
            }"
            x-on:mouseenter="stopAutoplay()"
            x-on:mouseleave="startAutoplay()"
            class="haunts-carousel"
            data-testid="other-haunts-carousel"
            data-visible-desktop="4"
            data-visible-tablet="2"
            data-visible-mobile="1"
        >
            <div class="haunts-carousel-viewport">
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-4">
                    @foreach ($cards as $index => $card)
                        <div
                            x-show="isVisible({{ $index }})"
                            x-transition:enter="transition ease-out duration-400"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-250"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-2"
                            data-testid="other-haunt-card-{{ $index }}"
                        >
                            <x-featured-card
                                :title="$card['title']"
                                :description="$card['description']"
                                :images="$card['images'] ?? []"
                                :image="$card['image'] ?? null"
                                :link="$card['href'] ?? null"
                                :link-text="$card['linkText'] ?? 'Learn More'"
                            />
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="haunts-carousel-controls">
                <button
                    type="button"
                    class="haunts-carousel-button"
                    x-on:click="prev()"
                    x-bind:disabled="pages <= 1"
                    data-testid="other-haunts-prev"
                    aria-label="Previous haunts"
                >
                    <span class="material-symbols-outlined">chevron_left</span>
                </button>

                <div class="haunts-carousel-dots" x-show="pages > 1">
                    <template x-for="dot in pages" :key="'haunts-dot-' + dot">
                        <button
                            type="button"
                            class="haunts-carousel-dot"
                            x-on:click="goTo(dot - 1)"
                            x-bind:class="{ 'is-active': page === dot - 1 }"
                            x-bind:aria-label="'Go to haunts slide ' + dot"
                        ></button>
                    </template>
                </div>

                <button
                    type="button"
                    class="haunts-carousel-button"
                    x-on:click="next()"
                    x-bind:disabled="pages <= 1"
                    data-testid="other-haunts-next"
                    aria-label="Next haunts"
                >
                    <span class="material-symbols-outlined">chevron_right</span>
                </button>
            </div>
        </div>
    @endif
</section>
