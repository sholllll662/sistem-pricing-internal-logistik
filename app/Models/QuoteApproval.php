<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteApproval extends Model
{
    public const DECISION_PENDING = 'pending';
    public const DECISION_APPROVED = 'approved';
    public const DECISION_REJECTED = 'rejected';

    protected $fillable = [
        'quote_id',
        'approver_user_id',
        'decision',
        'decision_notes',
        'decided_at',
    ];

    protected function casts(): array
    {
        return [
            'decided_at' => 'datetime',
        ];
    }

    public static function decisions(): array
    {
        return [
            self::DECISION_PENDING,
            self::DECISION_APPROVED,
            self::DECISION_REJECTED,
        ];
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_user_id');
    }
}
