<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegCostItem extends Model
{
    protected $fillable = [
        'leg_id',
        'cost_category_id',
        'vendor_id',
        'item_name',
        'description',
        'quantity',
        'unit_name',
        'unit_price',
        'line_total',
        'price_source_date',
        'price_source_reference',
        'is_manual_override',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:4',
            'unit_price' => 'decimal:2',
            'line_total' => 'decimal:2',
            'price_source_date' => 'date',
            'is_manual_override' => 'boolean',
        ];
    }

    public function leg(): BelongsTo
    {
        return $this->belongsTo(ScenarioLeg::class, 'leg_id');
    }

    public function costCategory(): BelongsTo
    {
        return $this->belongsTo(CostCategory::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }
}
