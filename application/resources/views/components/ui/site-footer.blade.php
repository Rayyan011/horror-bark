@props([
    'links' => [],
    'copyright' => null,
])

<footer class="bg-black text-gray-400 p-6 mt-12">
    <div class="container mx-auto text-center space-y-2">
        <p>{{ $copyright ?: '© ' . date('Y') . ' Horror-Bark. All rights reserved.' }}</p>

        @if (!empty($links))
            <p class="space-x-2">
                @foreach ($links as $index => $link)
                    <a href="{{ $link['href'] }}" class="hover:text-red-400">{{ $link['label'] }}</a>
                    @if ($index < count($links) - 1)
                        <span>|</span>
                    @endif
                @endforeach
            </p>
        @endif
    </div>
</footer>
