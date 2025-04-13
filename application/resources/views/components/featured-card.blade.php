@props(['title', 'description', 'image' => null, 'images' => [], 'link' => null, 'linkText' => 'Learn More'])

<div class="bg-gray-800 shadow-lg rounded overflow-hidden border border-gray-700">
    @if ($image)
        <img src="{{ $image }}" alt="{{ $title }}" class="w-full h-48 object-cover" />
    @elseif (count($images) > 0)
        <x-image-carousel :images="$images" :title="$title" />
    @endif
    <div class="p-4">
        <h4 class="font-bold text-xl mb-2 horror-font">{{ $title }}</h4>
        <p class="text-gray-300 text-base">{{ $description }}</p>
        @if($link)
            <a href="{{ $link }}" class="inline-block mt-4 text-red-400 hover:underline">{{ $linkText }}</a>
        @endif
    </div>
</div>