@props([
    'heading' => null,
    'description' => null,
    'periodOptions' => [],
    'stats' => [],
])

<div class="grid gap-y-4">
    @if ($heading || $description || $periodOptions)
        <div class="flex flex-col gap-y-2 sm:flex-row sm:items-center sm:justify-between">
            <div class="grid gap-y-1">
                @if ($heading)
                    <h3 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">{{ $heading }}</h3>
                @endif
                @if ($description)
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $description }}</p>
                @endif
            </div>

            @if ($periodOptions)
                <select class="w-max px-3 py-2 rounded bg-gray-900 border border-gray-700 text-white">
                    @foreach ($periodOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            @endif
        </div>
    @endif

    <div class="grid gap-6 md:grid-cols-3">
        @foreach ($stats as $stat)
            {{ $stat }}
        @endforeach
    </div>
</div>
