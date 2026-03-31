<?php

namespace App\Observers;

use App\Services\AuditLogger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditableModelObserver
{
    public function __construct(private readonly AuditLogger $auditLogger) {}

    public function created(Model $model): void
    {
        $this->writeAuditLog('created', $model, null, $model->getAttributes());
    }

    public function updated(Model $model): void
    {
        $this->writeAuditLog('updated', $model, $model->getOriginal(), $model->getAttributes());
    }

    public function deleted(Model $model): void
    {
        $this->writeAuditLog('deleted', $model, $model->getOriginal(), null);
    }

    private function writeAuditLog(string $action, Model $model, ?array $before, ?array $after): void
    {
        $actor = Auth::user();

        if (! $actor || ! $actor->hasAnyRole(['super_admin', 'admin', 'hotel_manager', 'ferry_manager', 'ride_manager', 'game_manager'])) {
            return;
        }

        $this->auditLogger->log(
            $actor,
            sprintf('%s.%s', strtolower(class_basename($model)), $action),
            $model,
            $before,
            $after
        );
    }
}
