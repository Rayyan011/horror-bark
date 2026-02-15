@props([
    'paginator',
    'compact' => false,
    'anchor' => null,
])

@if (method_exists($paginator, 'hasPages') && $paginator->hasPages())
    <div {{ $attributes->class([$compact ? 'mt-4' : 'mt-6']) }}>
        {{ $paginator->links() }}
    </div>
@endif
