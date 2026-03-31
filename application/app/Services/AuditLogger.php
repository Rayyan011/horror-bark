<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AuditLogger
{
    public function log(
        ?User $actor,
        string $action,
        Model $auditable,
        ?array $before = null,
        ?array $after = null,
        array $metadata = []
    ): AuditLog {
        return AuditLog::create([
            'actor_id' => $actor?->id,
            'action' => $action,
            'auditable_type' => $auditable::class,
            'auditable_id' => $auditable->getKey(),
            'before_state' => $before,
            'after_state' => $after,
            'metadata' => $metadata,
            'occurred_at' => now(),
        ]);
    }
}
