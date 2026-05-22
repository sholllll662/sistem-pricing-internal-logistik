<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleType extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'capacity_notes',
    ];
}
