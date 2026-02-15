@props([
    'items' => [],
    'defaultOpen' => null,
    'countBadge' => true,
])

<div class="space-y-3">
    @foreach ($items as $index => $item)
        @php
            $isOpen = !is_null($defaultOpen)
                ? ($defaultOpen === ($item['key'] ?? $index))
                : ($item['open'] ?? false);
        @endphp

        <details class="bg-gray-800 rounded border border-gray-700" @if($isOpen) open @endif>
            <summary class="cursor-pointer list-none p-4 flex items-center justify-between">
                <span class="text-xl font-semibold">{{ $item['label'] ?? 'Section' }}</span>
                @if ($countBadge)
                    <span class="text-sm text-gray-400">{{ $item['count'] ?? 0 }}</span>
                @endif
            </summary>

            <div class="px-4 pb-4">
                @if (isset($item['slot']))
                    {!! $item['slot'] !!}
                @elseif (isset($item['content']))
                    {!! $item['content'] !!}
                @endif
            </div>
        </details>
    @endforeach
</div>
