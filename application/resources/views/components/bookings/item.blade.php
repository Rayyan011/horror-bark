@props([
    'item',
])

<article class="theme-list-card">
    <div class="flex flex-wrap justify-between gap-4">
        <div>
            <p class="theme-kicker">{{ $item['type_label'] }}</p>
            <p class="mt-1 text-xl font-semibold text-moonlight">{{ $item['title'] }}</p>
            <p class="readable-muted">{{ $item['subtitle'] }}</p>
            <p class="readable-muted">Schedule: {{ $item['schedule'] }}</p>
            <p class="readable-muted">Quantity: {{ $item['quantity'] }}</p>
        </div>

        <div class="text-right">
            <p class="theme-total-value !mt-0 !text-lg">MVR {{ number_format($item['total_price'], 2) }}</p>
            <p class="readable-muted">Status: {{ ucfirst($item['status']) }}</p>
            <a href="{{ $item['detail_url'] }}" class="theme-inline-link">View details</a>

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
