<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
        'name',
        'is_main',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country',
        'availability',
        'phone',
        'whatsapp',
        'google_maps_link',
        'meta_title',
        'meta_description',
        'json_schema',
        'sort_order',
    ];

    protected $casts = [
        'is_main' => 'boolean',
        'json_schema' => 'array',
    ];
}
