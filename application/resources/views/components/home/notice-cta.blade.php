@props([
    'message',
])

<div class="bg-red-800 border border-red-900 text-red-300 p-4 rounded mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
    <span><strong>Important:</strong> {{ $message }}</span>
    <div>
        {{ $slot }}
    </div>
</div>
