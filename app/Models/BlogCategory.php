<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'image',
        'image_media_id',
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

    public function imageMedia()
    {
        return $this->belongsTo(Media::class, 'image_media_id');
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->imageMedia) {
            return $this->imageMedia->url;
        }
        if (!$this->image) {
            return '/assets/img/logo.jpeg';
        }
        if (str_starts_with($this->image, 'assets/') || str_starts_with($this->image, '/assets/')) {
            return '/' . ltrim($this->image, '/');
        }
        return \Illuminate\Support\Facades\Storage::url($this->image);
    }

    public function posts()
    {
        return $this->hasMany(BlogPost::class);
    }
}
