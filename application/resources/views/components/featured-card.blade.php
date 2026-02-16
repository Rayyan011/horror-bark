@props(['title', 'description', 'image' => null, 'images' => [], 'link' => null, 'linkText' => 'Learn More'])

@php
    $resolvedImage = collect($images)->first(fn ($item) => filled($item));

    if ($resolvedImage && !str_starts_with($resolvedImage, 'http://') && !str_starts_with($resolvedImage, 'https://') && !str_starts_with($resolvedImage, '/')) {
        $resolvedImage = asset('storage/' . ltrim($resolvedImage, '/'));
    }

    if (!$resolvedImage && filled($image)) {
        if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://') || str_starts_with($image, '/')) {
            $resolvedImage = $image;
        } else {
            $resolvedImage = asset($image);
        }
    }
@endphp

<article class="group relative cursor-pointer overflow-hidden rounded-sm border border-primary-light/20 bg-primary-dark shadow-cold-shadow transition duration-500 hover:border-primary-light/55">
    <div class="aspect-[3/4] overflow-hidden">
        @if ($resolvedImage)
            <img
                src="{{ $resolvedImage }}"
                alt="{{ $title }}"
                class="h-full w-full object-cover grayscale-[70%] brightness-[0.5] contrast-110 transition duration-1000 group-hover:scale-110 group-hover:brightness-[0.72]"
                loading="lazy"
            />
        @else
            <div class="h-full w-full bg-cold-gradient"></div>
        @endif
    </div>

    <div class="absolute inset-0 bg-gradient-to-t from-background-dark via-transparent to-transparent opacity-85"></div>

    <div class="absolute inset-x-0 bottom-0 border-t border-primary-light/20 bg-background-dark/85 p-8 backdrop-blur-md">
        <h3 class="mb-2 font-display text-2xl tracking-wide text-moonlight transition-colors group-hover:text-white">{{ $title }}</h3>
        <p class="mb-4 font-serif text-sm leading-relaxed text-primary-light transition-colors group-hover:text-moonlight">
            {{ \Illuminate\Support\Str::limit($description, 130) }}
        </p>

        @if ($link)
            <a href="{{ $link }}" class="gothic-link">{{ $linkText }}</a>
        @endif
    </div>
</article>
