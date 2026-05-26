<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    public const EVENT_CREATED = 'created';
    public const EVENT_UPDATED = 'updated';
    public const EVENT_STATUS_CHANGED = 'status_changed';
    public const EVENT_APPROVAL_DECIDED = 'approval_decided';

    protected $fillable = [
        'auditable_type',
        'auditable_id',
        'event_name',
        'old_values_jsonb',
        'new_values_jsonb',
        'changed_by_user_id',
        'changed_at',
    ];

    protected function casts(): array
    {
        return [
            'old_values_jsonb' => 'array',
            'new_values_jsonb' => 'array',
            'changed_at' => 'datetime',
        ];
    }

    public static function events(): array
    {
        return [
            self::EVENT_CREATED,
            self::EVENT_UPDATED,
            self::EVENT_STATUS_CHANGED,
            self::EVENT_APPROVAL_DECIDED,
        ];
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}
