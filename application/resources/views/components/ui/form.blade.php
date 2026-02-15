@props([
    'method' => 'POST',
    'action' => '',
    'csrf' => true,
])

@php
    $normalizedMethod = strtoupper($method);
    $httpMethod = $normalizedMethod === 'GET' ? 'GET' : 'POST';
@endphp

<form method="{{ $httpMethod }}" action="{{ $action }}" {{ $attributes }}>
    @if ($csrf && $normalizedMethod !== 'GET')
        @csrf
    @endif

    @if (!in_array($normalizedMethod, ['GET', 'POST'], true))
        @method($normalizedMethod)
    @endif

    {{ $slot }}
</form>
