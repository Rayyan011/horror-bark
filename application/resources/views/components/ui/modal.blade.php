@props([
    'id',
    'title' => null,
    'open' => false,
    'size' => 'md',
])

@php
    $sizeClass = match ($size) {
        'sm' => 'max-w-md',
        'lg' => 'max-w-2xl',
        'xl' => 'max-w-4xl',
        default => 'max-w-lg',
    };
@endphp

<div id="{{ $id }}" class="{{ $open ? '' : 'hidden' }} fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50 px-4" role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-title">
    <div class="bg-gray-900 p-6 rounded-lg w-full {{ $sizeClass }} relative">
        <button type="button" onclick="closeUiModal('{{ $id }}')" class="absolute top-2 right-2 text-white text-xl" aria-label="Close">&times;</button>

        @if ($title)
            <h3 id="{{ $id }}-title" class="text-2xl font-bold mb-4 text-white">{{ $title }}</h3>
        @endif

        <div>
            @isset($body)
                {{ $body }}
            @else
                {{ $slot }}
            @endisset
        </div>

        @isset($footer)
            <div class="mt-4 border-t border-gray-700 pt-4">
                {{ $footer }}
            </div>
        @endisset
    </div>
</div>

@once
    @push('scripts')
        <script>
            function openUiModal(id) {
                const el = document.getElementById(id);
                if (el) el.classList.remove('hidden');
            }

            function closeUiModal(id) {
                const el = document.getElementById(id);
                if (el) el.classList.add('hidden');
            }
        </script>
    @endpush
@endonce
