<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    protected $fillable = [
        'code',
        'name',
        'vendor_type',
        'address',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function vendorContacts(): HasMany
    {
        return $this->hasMany(VendorContact::class);
    }

    public function legCostItems(): HasMany
    {
        return $this->hasMany(LegCostItem::class);
    }
}
