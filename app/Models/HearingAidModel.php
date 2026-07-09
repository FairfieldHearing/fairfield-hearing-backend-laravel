<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HearingAidModel extends Model
{
    protected $fillable = [
        'manufacturer_id',
        'name',
        'mrp',
        'discount_pct',
        'key_features',
        'tags',
        'tech_level',
        'form_factor',
        'units',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'key_features' => 'array',
        'tags' => 'array',
        'is_active' => 'boolean',
        'mrp' => 'integer',
        'discount_pct' => 'float',
        'units' => 'integer',
        'sort_order' => 'integer',
    ];

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class, 'manufacturer_id');
    }
}
