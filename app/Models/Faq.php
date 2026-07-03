<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $fillable = [
        'blog_post_id',
        'question',
        'answer',
        'type',
        'json_schema',
        'sort_order',
    ];

    protected $casts = [
        'json_schema' => 'array',
    ];

    public function post()
    {
        return $this->belongsTo(BlogPost::class, 'blog_post_id');
    }
}
