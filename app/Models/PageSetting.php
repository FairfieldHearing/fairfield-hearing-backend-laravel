<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageSetting extends Model
{
    protected $fillable = [
        'page_key',
        'page_name',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'json_schema',
    ];

    protected $casts = [
        'json_schema' => 'array',
    ];
}
