<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Quote extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_WAITING_APPROVAL = 'waiting_approval';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_SENT = 'sent';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_EXPIRED = 'expired';

    protected $fillable = [
        'quote_number',
        'inquiry_id',
        'scenario_id',
        'prepared_by_user_id',
        'valid_from',
        'valid_until',
        'total_base_cost_snapshot',
        'total_margin_snapshot',
        'total_selling_price_snapshot',
        'status',
        'approval_status',
        'customer_notes',
        'internal_notes',
        'sent_at',
        'accepted_at',
        'rejected_at',
        'expired_at',
    ];

    protected function casts(): array
    {
        return [
            'valid_from' => 'date',
            'valid_until' => 'date',
            'total_base_cost_snapshot' => 'decimal:2',
            'total_margin_snapshot' => 'decimal:2',
            'total_selling_price_snapshot' => 'decimal:2',
            'sent_at' => 'datetime',
            'accepted_at' => 'datetime',
            'rejected_at' => 'datetime',
            'expired_at' => 'datetime',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT,
            self::STATUS_WAITING_APPROVAL,
            self::STATUS_APPROVED,
            self::STATUS_SENT,
            self::STATUS_ACCEPTED,
            self::STATUS_REJECTED,
            self::STATUS_EXPIRED,
        ];
    }

    public function inquiry(): BelongsTo
    {
        return $this->belongsTo(Inquiry::class);
    }

    public function scenario(): BelongsTo
    {
        return $this->belongsTo(InquiryScenario::class, 'scenario_id');
    }

    public function preparedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by_user_id');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(QuoteApproval::class);
    }

    public static function defaultValidFrom(): Carbon
    {
        return Carbon::today();
    }

    public static function defaultValidUntil(?Carbon $validFrom = null): Carbon
    {
        $from = $validFrom ? $validFrom->copy() : self::defaultValidFrom();

        return $from->copy()->addMonths(3);
    }

    public static function isValidityRangeAllowed(Carbon $validFrom, Carbon $validUntil): bool
    {
        if ($validUntil->lt($validFrom)) {
            return false;
        }

        $minimum = $validFrom->copy()->addMonths(3);
        $maximum = $validFrom->copy()->addMonths(6);

        return $validUntil->betweenIncluded($minimum, $maximum);
    }
}
