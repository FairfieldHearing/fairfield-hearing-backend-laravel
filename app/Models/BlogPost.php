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
        'meta_keywords',
        'canonical_url',
    ];

    protected $casts = [
        'json_schema' => 'array',
    ];

    public function getFeaturedImageUrlAttribute(): string
    {
        if (!$this->featured_image) {
            return '/assets/img/logo.jpeg';
        }
        if (str_starts_with($this->featured_image, 'assets/') || str_starts_with($this->featured_image, '/assets/')) {
            return '/' . ltrim($this->featured_image, '/');
        }
        return \Illuminate\Support\Facades\Storage::url($this->featured_image);
    }

    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function faqs()
    {
        return $this->hasMany(Faq::class)->where('type', 'blog_post');
    }
}
