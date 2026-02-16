@props([
    'image' => null,
    'overlay' => 'bg-gradient-to-b from-background-dark/70 via-primary-dark/45 to-background-dark',
    'title' => null,
    'subtitle' => null,
    'eyebrow' => 'Under the Pale Moon',
    'height' => 'h-[92vh]',
])

<section class="relative flex items-center justify-center overflow-hidden {{ $height }}">
    <div class="absolute inset-0 z-0">
        @if ($image)
            <img
                src="{{ $image }}"
                alt="{{ $title ?: 'Horror-Bark hero backdrop' }}"
                class="h-full w-full object-cover grayscale-[70%] brightness-[0.5] contrast-110"
            />
        @else
            <div class="h-full w-full bg-cold-gradient"></div>
        @endif

        <div class="absolute inset-0 {{ $overlay }}"></div>
        <div class="absolute inset-0 bg-damask-pattern opacity-20"></div>
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] opacity-10"></div>
    </div>

    <div class="relative z-10 mx-auto w-full max-w-5xl px-4 text-center">
        @if (filled($eyebrow))
            <p class="mb-6 font-serif text-sm uppercase tracking-[0.5em] text-primary-light md:text-base">{{ $eyebrow }}</p>
        @endif

        @if ($title)
            <h2 class="mb-8 font-display text-5xl leading-tight text-white drop-shadow-[0_5px_15px_rgba(0,0,0,1)] md:text-6xl">
                <span class="text-metallic">{{ $title }}</span>
            </h2>
        @endif

        @if ($subtitle)
            <p class="mx-auto mb-10 max-w-3xl font-serif text-xl font-light leading-relaxed tracking-wide text-primary-light md:text-2xl">
                {{ $subtitle }}
            </p>
        @endif

        @if ($slot->isNotEmpty())
            <div class="flex flex-col justify-center gap-5 sm:flex-row">
                {{ $slot }}
            </div>
        @endif
    </div>
</section>
