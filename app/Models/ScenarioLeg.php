<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScenarioLeg extends Model
{
    public const TYPE_FIRST_MILE = 'first_mile';
    public const TYPE_MIDDLE_MILE = 'middle_mile';
    public const TYPE_LAST_MILE = 'last_mile';
    public const TYPE_CUSTOM = 'custom';

    protected $fillable = [
        'scenario_id',
        'sequence_no',
        'leg_type',
        'origin_location_id',
        'destination_location_id',
        'transport_mode_id',
        'vehicle_type_id',
        'primary_vendor_id',
        'distance_notes',
        'lead_time_notes',
        'operation_notes',
        'base_cost_snapshot',
        'metadata_jsonb',
    ];

    protected function casts(): array
    {
        return [
            'base_cost_snapshot' => 'decimal:2',
            'metadata_jsonb' => 'array',
        ];
    }

    public static function legTypes(): array
    {
        return [
            self::TYPE_FIRST_MILE,
            self::TYPE_MIDDLE_MILE,
            self::TYPE_LAST_MILE,
            self::TYPE_CUSTOM,
        ];
    }

    public function scenario(): BelongsTo
    {
        return $this->belongsTo(InquiryScenario::class, 'scenario_id');
    }

    public function originLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'origin_location_id');
    }

    public function destinationLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'destination_location_id');
    }

    public function transportMode(): BelongsTo
    {
        return $this->belongsTo(TransportMode::class, 'transport_mode_id');
    }

    public function vehicleType(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class, 'vehicle_type_id');
    }

    public function primaryVendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'primary_vendor_id');
    }
}
