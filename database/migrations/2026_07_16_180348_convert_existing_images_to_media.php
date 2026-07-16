<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $createMediaItem = function ($path, $folder) {
            if (empty($path)) {
                return null;
            }

            // Check if media already exists
            $existing = DB::table('media')->where('filepath', $path)->first();
            if ($existing) {
                return $existing->id;
            }

            $filename = basename($path);
            $mimeType = 'image/jpeg'; // fallback
            $size = 0;
            $width = null;
            $height = null;

            // Try to find file in public folder or storage folder to get correct details
            $realPath = public_path($path);
            if (!file_exists($realPath)) {
                $realPath = storage_path('app/public/' . $path);
            }

            if (file_exists($realPath) && !is_dir($realPath)) {
                $size = filesize($realPath);
                $info = @getimagesize($realPath);
                if ($info) {
                    $width = $info[0];
                    $height = $info[1];
                    $mimeType = $info['mime'];
                }
            }

            return DB::table('media')->insertGetId([
                'filename' => $filename,
                'filepath' => $path,
                'original_filename' => $filename,
                'mime_type' => $mimeType,
                'size' => $size,
                'folder' => $folder,
                'width' => $width,
                'height' => $height,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        };

        // Convert Blog Posts
        $posts = DB::table('blog_posts')->get();
        foreach ($posts as $post) {
            if ($post->featured_image) {
                $mediaId = $createMediaItem($post->featured_image, 'blog_posts');
                if ($mediaId) {
                    DB::table('blog_posts')->where('id', $post->id)->update([
                        'featured_image_media_id' => $mediaId
                    ]);
                }
            }
        }

        // Convert Blog Categories
        $categories = DB::table('blog_categories')->get();
        foreach ($categories as $cat) {
            if ($cat->image) {
                $mediaId = $createMediaItem($cat->image, 'categories');
                if ($mediaId) {
                    DB::table('blog_categories')->where('id', $cat->id)->update([
                        'image_media_id' => $mediaId
                    ]);
                }
            }
        }

        // Convert Manufacturers
        $manufacturers = DB::table('manufacturers')->get();
        foreach ($manufacturers as $man) {
            if ($man->logo_path) {
                $mediaId = $createMediaItem($man->logo_path, 'manufacturers');
                if ($mediaId) {
                    DB::table('manufacturers')->where('id', $man->id)->update([
                        'logo_media_id' => $mediaId
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nullify relations
        DB::table('blog_posts')->update(['featured_image_media_id' => null]);
        DB::table('blog_categories')->update(['image_media_id' => null]);
        DB::table('manufacturers')->update(['logo_media_id' => null]);
    }
};
