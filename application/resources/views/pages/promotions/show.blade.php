@extends('layouts.app')

@section('title', $promotion->resolved_title.' - Horror-Bark')

@section('content')
<main class="space-y-10">
    <section class="overflow-hidden rounded-[2rem] border border-primary-light/15 bg-[linear-gradient(135deg,_rgba(8,7,12,0.98),_rgba(21,16,30,0.92))] shadow-[0_30px_80px_rgba(0,0,0,0.42)]">
        <div class="grid lg:grid-cols-[1.1fr,0.9fr]">
            <div class="space-y-5 px-6 py-10 sm:px-10 lg:px-12 lg:py-14">
                <span class="catalog-range-pill">{{ $offer['badge'] }}</span>
                <p class="theme-kicker">{{ $offer['kicker'] }}</p>
                <h1 class="font-display text-4xl italic leading-none text-moonlight sm:text-5xl">{{ $offer['heading'] }}</h1>
                <p class="readable-copy max-w-3xl">{{ $offer['lede'] }}</p>
                <p class="readable-muted max-w-2xl">{{ $offer['summary'] }}</p>

                <div class="flex flex-wrap gap-3 pt-2">
                    <x-ui.button :href="$offer['catalog_href']" variant="secondary">
                        {{ $offer['catalog_label'] }}
                    </x-ui.button>
                    <x-ui.button :href="route('home')" variant="ghost">
                        Return Home
                    </x-ui.button>
                </div>
            </div>

            <div class="min-h-[18rem] border-l border-primary-light/10 bg-black/50">
                <img
                    src="{{ $offer['hero_image'] }}"
                    alt="{{ $promotion->resolved_title }}"
                    class="h-full w-full object-cover grayscale-[20%] brightness-[0.74] contrast-110"
                />
            </div>
        </div>
    </section>

    <x-ui.alert-stack />

    <section class="space-y-4">
        <x-ui.section-heading
            title="Book The Discounted Selection"
            subtitle="Choose an eligible item below and the reduced rate will carry into checkout."
            size="lg"
            align="center"
        />

        <div class="grid gap-6 xl:grid-cols-3">
            @foreach ($offer['items'] as $item)
                <x-promotions.offer-card :item="$item" :promotion="$promotion" />
            @endforeach
        </div>
    </section>
</main>
@endsection
