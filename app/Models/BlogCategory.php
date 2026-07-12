<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'image',
        'short_description',
        'meta_title',
        'meta_description',
        'json_schema',
        'meta_keywords',
        'canonical_url',
    ];

    protected $casts = [
        'json_schema' => 'array',
    ];

    public function posts()
    {
        return $this->hasMany(BlogPost::class);
    }
}
