<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inquiry extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_PRICING_IN_PROGRESS = 'pricing_in_progress';
    public const STATUS_WAITING_APPROVAL = 'waiting_approval';
    public const STATUS_QUOTED = 'quoted';
    public const STATUS_CLOSED = 'closed';
    public const STATUS_CANCELED = 'canceled';

    protected $fillable = [
        'inquiry_number',
        'customer_id',
        'sales_owner_id',
        'pickup_contact_id',
        'drop_contact_id',
        'origin_location_id',
        'destination_location_id',
        'cargo_name',
        'cargo_description',
        'cargo_weight',
        'cargo_volume',
        'cargo_dimension_notes',
        'service_notes',
        'status',
        'submitted_at',
        'closed_at',
        'metadata_jsonb',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'closed_at' => 'datetime',
            'metadata_jsonb' => 'array',
            'cargo_weight' => 'decimal:3',
            'cargo_volume' => 'decimal:3',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT,
            self::STATUS_SUBMITTED,
            self::STATUS_PRICING_IN_PROGRESS,
            self::STATUS_WAITING_APPROVAL,
            self::STATUS_QUOTED,
            self::STATUS_CLOSED,
            self::STATUS_CANCELED,
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function salesOwner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sales_owner_id');
    }

    public function pickupContact(): BelongsTo
    {
        return $this->belongsTo(CustomerContact::class, 'pickup_contact_id');
    }

    public function dropContact(): BelongsTo
    {
        return $this->belongsTo(CustomerContact::class, 'drop_contact_id');
    }

    public function originLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'origin_location_id');
    }

    public function destinationLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'destination_location_id');
    }

    public function inquiryScenarios(): HasMany
    {
        return $this->hasMany(InquiryScenario::class);
    }
}
