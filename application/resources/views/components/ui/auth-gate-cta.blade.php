@props([
    'loginHref' => null,
    'label' => 'Log in to book',
])

<x-ui.button :href="($loginHref ?: route('login')) . '?redirect=' . urlencode(request()->fullUrl())" variant="primary" block>
    {{ $label }}
</x-ui.button>
