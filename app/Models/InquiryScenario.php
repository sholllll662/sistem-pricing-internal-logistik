<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InquiryScenario extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_CALCULATED = 'calculated';
    public const STATUS_SELECTED = 'selected';
    public const STATUS_ARCHIVED = 'archived';

    protected $fillable = [
        'inquiry_id',
        'scenario_code',
        'scenario_name',
        'description',
        'status',
        'is_selected',
        'total_base_cost_snapshot',
        'total_margin_snapshot',
        'total_selling_price_snapshot',
        'calculation_notes',
        'metadata_jsonb',
    ];

    protected function casts(): array
    {
        return [
            'is_selected' => 'boolean',
            'total_base_cost_snapshot' => 'decimal:2',
            'total_margin_snapshot' => 'decimal:2',
            'total_selling_price_snapshot' => 'decimal:2',
            'metadata_jsonb' => 'array',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT,
            self::STATUS_CALCULATED,
            self::STATUS_SELECTED,
            self::STATUS_ARCHIVED,
        ];
    }

    public function inquiry(): BelongsTo
    {
        return $this->belongsTo(Inquiry::class);
    }

    public function scenarioLegs(): HasMany
    {
        return $this->hasMany(ScenarioLeg::class, 'scenario_id');
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class, 'scenario_id');
    }
}
