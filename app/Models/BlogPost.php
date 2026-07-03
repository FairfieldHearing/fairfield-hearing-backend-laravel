<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    protected $fillable = [
        'blog_category_id',
        'title',
        'slug',
        'summary',
        'featured_image',
        'content',
        'author_name',
        'meta_title',
        'meta_description',
        'json_schema',
    ];

    protected $casts = [
        'json_schema' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function faqs()
    {
        return $this->hasMany(Faq::class)->where('type', 'blog_post');
    }
}
