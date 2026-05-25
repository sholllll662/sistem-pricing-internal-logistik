<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CostCategory extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
    ];

    public function legCostItems(): HasMany
    {
        return $this->hasMany(LegCostItem::class);
    }
}
