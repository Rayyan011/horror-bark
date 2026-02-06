<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

abstract class QuickActionsWidget extends Widget
{
    protected static string $view = 'filament.widgets.quick-actions';

    protected static ?int $sort = 0;

    /**
     * @return array<int, array{label: string, url: string, icon?: string, color?: string}>
     */
    protected function getActions(): array
    {
        return [];
    }

    protected function getHeading(): string
    {
        return 'Quick actions';
    }

    protected function shouldShowEmptyState(): bool
    {
        return false;
    }

    protected function getEmptyStateMessage(): ?string
    {
        return null;
    }
}
