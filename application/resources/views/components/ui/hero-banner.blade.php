@props([
    'image' => null,
    'overlay' => 'bg-black bg-opacity-60',
    'title' => null,
    'subtitle' => null,
    'height' => 'h-72',
])

<section
    class="bg-cover bg-center {{ $height }}"
    @if ($image)
        style="background-image: url('{{ $image }}');"
    @endif
>
    <div class="{{ $overlay }} h-full flex items-center justify-center">
        <div class="text-center text-white px-4">
            @if ($title)
                <h2 class="text-4xl font-bold mb-4 horror-font">{{ $title }}</h2>
            @endif

            @if ($subtitle)
                <p class="text-lg">{{ $subtitle }}</p>
            @endif

            {{ $slot }}
        </div>
    </div>
</section>
