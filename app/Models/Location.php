<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
        'code',
        'name',
        'location_type',
        'country',
        'province',
        'city',
        'district',
        'postal_code',
        'address',
        'latitude',
        'longitude',
    ];
}
