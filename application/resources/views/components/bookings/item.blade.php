@props([
    'item',
])

<article class="bg-gray-900 p-4 rounded border border-gray-700">
    <div class="flex flex-wrap justify-between gap-4">
        <div>
            <p class="text-xs uppercase tracking-wide text-gray-400">{{ $item['type_label'] }}</p>
            <p class="text-white font-semibold">{{ $item['title'] }}</p>
            <p class="text-gray-400 text-sm">{{ $item['subtitle'] }}</p>
            <p class="text-gray-400 text-sm">Schedule: {{ $item['schedule'] }}</p>
            <p class="text-gray-400 text-sm">Quantity: {{ $item['quantity'] }}</p>
        </div>

        <div class="text-right">
            <p class="text-gray-300 text-sm">Total: MVR {{ number_format($item['total_price'], 2) }}</p>
            <p class="text-gray-400 text-sm">Status: {{ ucfirst($item['status']) }}</p>
            <a href="{{ $item['detail_url'] }}" class="text-sm text-red-300 hover:text-red-200">View details</a>

            @if (!empty($item['can_cancel']) && !empty($item['cancel_url']))
                <form method="POST" action="{{ $item['cancel_url'] }}" class="mt-2">
                    @csrf
                    @method('PATCH')
                    <x-ui.button type="submit" variant="danger" size="sm">Cancel</x-ui.button>
                </form>
            @endif
        </div>
    </div>
</article>
