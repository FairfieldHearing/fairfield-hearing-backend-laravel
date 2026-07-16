<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class Media extends Model
{
    protected $fillable = [
        'filename',
        'filepath',
        'original_filename',
        'mime_type',
        'size',
        'folder',
        'width',
        'height',
    ];

    /**
     * Upload and compress an image file, registering it in media library.
     */
    public static function upload(UploadedFile $file, string $folder = 'general'): self
    {
        // Compress first (limits to 1000x1000 by default)
        \App\Helpers\ImageHelper::compressAndResize($file);

        // Store
        $path = $file->store('media/' . $folder, 'public');

        // Extract dimensions if possible
        $width = null;
        $height = null;
        if (extension_loaded('gd')) {
            $realPath = Storage::disk('public')->path($path);
            if (file_exists($realPath)) {
                $info = getimagesize($realPath);
                if ($info) {
                    $width = $info[0];
                    $height = $info[1];
                }
            }
        }

        return self::create([
            'filename' => basename($path),
            'filepath' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'folder' => $folder,
            'width' => $width,
            'height' => $height,
        ]);
    }

    /**
     * Get the public URL for the media item.
     */
    public function getUrlAttribute(): string
    {
        if (str_starts_with($this->filepath, 'assets/') || str_starts_with($this->filepath, '/assets/')) {
            return '/' . ltrim($this->filepath, '/');
        }
        return Storage::url($this->filepath);
    }

    /**
     * Retrieve a list of places where this media item is used.
     * Returns an array of items, each with:
     * - 'type': string (e.g. 'Blog Post', 'Blog Category', 'Manufacturer')
     * - 'name': string (the title/name of the item)
     * - 'url': string (optional edit link or view link in admin panel)
     */
    public function getUsageInfo(): array
    {
        $usages = [];
        $path = $this->filepath;
        $url = $this->url;

        // 1. Check Blog Posts (Featured Image, ID, or Content)
        $posts = BlogPost::where('featured_image_media_id', $this->id)
            ->orWhere('featured_image', $path)
            ->orWhere('featured_image', $url)
            ->orWhere('content', 'LIKE', '%' . $path . '%')
            ->orWhere('content', 'LIKE', '%' . $url . '%')
            ->get();

        foreach ($posts as $post) {
            $usages[] = [
                'type' => 'Blog Post',
                'name' => $post->title,
                'url' => route('admin.posts.edit', $post->id),
            ];
        }

        // 2. Check Blog Categories
        $categories = BlogCategory::where('image_media_id', $this->id)
            ->orWhere('image', $path)
            ->orWhere('image', $url)
            ->get();

        foreach ($categories as $cat) {
            $usages[] = [
                'type' => 'Blog Category',
                'name' => $cat->title,
                'url' => route('admin.categories'),
            ];
        }

        // 3. Check Manufacturers
        $manufacturers = Manufacturer::where('logo_media_id', $this->id)
            ->orWhere('logo_path', $path)
            ->orWhere('logo_path', $url)
            ->get();

        foreach ($manufacturers as $man) {
            $usages[] = [
                'type' => 'Manufacturer',
                'name' => $man->name,
                'url' => route('admin.manufacturers'),
            ];
        }

        // 4. Check Device Style Galleries
        $deviceGalleries = DeviceGallery::where('media_id', $this->id)->get();
        foreach ($deviceGalleries as $gallery) {
            $usages[] = [
                'type' => 'Device Gallery',
                'name' => strtoupper($gallery->style_slug) . ' Page Gallery',
                'url' => route('admin.media.galleries') . '?styleSlug=' . $gallery->style_slug,
            ];
        }

        return $usages;
    }
}
