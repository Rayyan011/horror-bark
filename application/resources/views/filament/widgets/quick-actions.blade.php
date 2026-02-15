@php
    $actions = $this->getActions();
    $heading = method_exists($this, 'getHeading') ? $this->getHeading() : 'Quick actions';
    $emptyStateMessage = method_exists($this, 'getEmptyStateMessage') ? $this->getEmptyStateMessage() : null;
    $showEmptyState = method_exists($this, 'shouldShowEmptyState') ? $this->shouldShowEmptyState() : false;
@endphp

<x-filament-widgets::widget>
    <x-filament::section :heading="$heading">
        <x-admin.quick-actions
            :actions="collect($actions)->map(fn ($action) => [
                'url' => $action['url'],
                'label' => $action['label'],
                'variant' => ($action['color'] ?? 'primary') === 'danger' ? 'danger' : 'primary',
            ])->values()->all()"
            :empty-state="$showEmptyState && filled($emptyStateMessage) ? $emptyStateMessage : null"
        />
    </x-filament::section>
</x-filament-widgets::widget>
