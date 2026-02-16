@props([
    'message',
])

<div class="midnight-protocol-panel mb-8 flex flex-col items-start justify-between gap-6 px-6 py-7 md:flex-row md:items-center md:px-12 md:py-9">
    <div class="flex w-full items-start gap-4 md:max-w-3xl">
        <span class="material-symbols-outlined midnight-protocol-icon mt-1">dark_mode</span>
        <div>
            <h3 class="midnight-protocol-title">Midnight Protocol</h3>
            <p class="midnight-protocol-message mt-2">
                {{ $message }}
            </p>
        </div>
    </div>

    <div class="w-full md:w-auto md:shrink-0">
        {{ $slot }}
    </div>
</div>
