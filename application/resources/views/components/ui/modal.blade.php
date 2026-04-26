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
        'wide' => 'max-w-[92rem]',
        default => 'max-w-lg',
    };
@endphp

<div id="{{ $id }}" class="{{ $open ? '' : 'hidden' }} fixed inset-0 z-50 overflow-y-auto bg-black/75 px-3 py-3 sm:px-5 sm:py-5" role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-title" data-ui-modal onclick="if (event.target === this) closeUiModal(this.id)">
    <div class="gothic-panel relative mx-auto w-full {{ $sizeClass }} rounded-lg p-4 sm:p-5">
        <button type="button" onclick="closeUiModal(this.closest('[data-ui-modal]').id)" class="absolute right-2 top-2 text-white text-xl" aria-label="Close">&times;</button>

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
            <div class="mt-4 border-t border-primary-light/15 pt-4">
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
                if (!el) return;

                el.classList.remove('hidden');
                window.activeUiModalId = id;
                document.addEventListener('keydown', handleUiModalKeydown);
            }

            function closeUiModal(id) {
                const el = document.getElementById(id);
                if (!el) return;

                el.classList.add('hidden');

                if (window.activeUiModalId === id) {
                    window.activeUiModalId = null;
                    document.removeEventListener('keydown', handleUiModalKeydown);
                }
            }

            function handleUiModalKeydown(event) {
                if (event.key === 'Escape' && window.activeUiModalId) {
                    closeUiModal(window.activeUiModalId);
                }
            }
        </script>
    @endpush
@endonce
