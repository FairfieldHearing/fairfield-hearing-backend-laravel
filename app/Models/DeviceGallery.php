<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceGallery extends Model
{
    protected $fillable = [
        'style_slug',
        'media_id',
        'is_featured',
        'sort_order',
    ];

    /**
     * Get the associated media item.
     */
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }
}
