<?php

namespace App\Services\Audit;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class AuditLogService
{
    public function log(
        Model $auditable,
        string $eventName,
        array $oldValues = [],
        array $newValues = [],
        ?int $changedByUserId = null,
        ?Carbon $changedAt = null
    ): AuditLog {
        return AuditLog::query()->create([
            'auditable_type' => $auditable::class,
            'auditable_id' => $auditable->getKey(),
            'event_name' => $eventName,
            'old_values_jsonb' => $oldValues,
            'new_values_jsonb' => $newValues,
            'changed_by_user_id' => $changedByUserId,
            'changed_at' => $changedAt ?? now(),
        ]);
    }
}
