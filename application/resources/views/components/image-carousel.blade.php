@props(['images', 'title'])

<div
    x-data="{
        currentSlide: 0,
        totalSlides: {{ count($images) }},
        interval: null
    }"
    x-init="
        if (totalSlides > 1) {
            interval = setInterval(() => {
                currentSlide = (currentSlide + 1) % totalSlides;
            }, 3000);
        }
    "
    x-on:mouseleave="
        if (totalSlides > 1 && !interval) {
            interval = setInterval(() => {
                currentSlide = (currentSlide + 1) % totalSlides;
            }, 3000);
        }
    "
    x-on:mouseenter="
        if (interval) {
            clearInterval(interval);
            interval = null;
        }
    "
    class="relative h-48 overflow-hidden"
>
    @foreach($images as $index => $image)
        <div
            x-show="currentSlide === {{ $index }}"
            x-transition:enter="transition ease-out duration-1000"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-1000 absolute inset-0"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="w-full h-full"
        >
            <img src="{{ asset('storage/' . $image) }}" alt="{{ $title }} - Image {{ $index + 1 }}" class="w-full h-48 object-cover" loading="lazy" />
        </div>
    @endforeach

    <div class="absolute bottom-2 left-1/2 transform -translate-x-1/2 flex space-x-2" x-show="totalSlides > 1">
        @foreach($images as $index => $image)
            <button
                @click="currentSlide = {{ $index }}; clearInterval(interval); interval = null;"
                :class="{ 'bg-white': currentSlide === {{ $index }}, 'bg-gray-400': currentSlide !== {{ $index }} }"
                class="w-2 h-2 rounded-full hover:bg-white focus:outline-none"
                aria-label="Go to slide {{ $index + 1 }}"
            ></button>
        @endforeach
    </div>
</div>
