<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Manufacturer extends Model
{
    protected $fillable = [
        'name',
        'logo_path',
        'logo_media_id',
        'is_active',
        'show_on_homepage',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_on_homepage' => 'boolean',
    ];

    public function logoMedia()
    {
        return $this->belongsTo(Media::class, 'logo_media_id');
    }

    public function getLogoUrlAttribute(): string
    {
        if ($this->logoMedia) {
            return $this->logoMedia->url;
        }
        if (!$this->logo_path) {
            return '/assets/img/logo.jpeg';
        }
        if (str_starts_with($this->logo_path, 'assets/') || str_starts_with($this->logo_path, '/assets/')) {
            return '/' . ltrim($this->logo_path, '/');
        }
        return \Illuminate\Support\Facades\Storage::url($this->logo_path);
    }

    public function hearingAidModels()
    {
        return $this->hasMany(HearingAidModel::class, 'manufacturer_id');
    }
}
