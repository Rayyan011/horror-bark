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

        <details class="theme-status-shell" @if($isOpen) open @endif>
            <summary class="theme-status-summary cursor-pointer list-none">
                <span class="text-xl font-semibold text-moonlight">{{ $item['label'] ?? 'Section' }}</span>
                @if ($countBadge)
                    <span class="theme-kicker">{{ $item['count'] ?? 0 }}</span>
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
