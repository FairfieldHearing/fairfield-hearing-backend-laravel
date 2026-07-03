<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'role',
        'category',
        'eyebrow',
        'photo',
        'short_bio',
        'at_a_glance',
        'areas_of_expertise',
        'blockquote',
        'bio',
        'timeline',
        'meta_title',
        'meta_description',
        'sort_order',
    ];

    protected $casts = [
        'at_a_glance' => 'array',
        'areas_of_expertise' => 'array',
        'timeline' => 'array',
    ];
}
