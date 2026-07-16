<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class ImageHelper
{
    /**
     * Resize and compress an uploaded image in place if it exceeds limits or needs compression.
     * Max dimensions: 1000x1000 (aspect ratio preserved, no upscaling).
     * Silently falls back if GD is missing or processing fails.
     *
     * @param mixed $file The uploaded file (TemporaryUploadedFile or UploadedFile)
     * @return void
     */
    public static function compressAndResize(mixed $file, int $maxWidth = 1000, int $maxHeight = 1000): void
    {
        if (!$file || !($file instanceof UploadedFile)) {
            return;
        }

        // Check if GD is available
        if (!extension_loaded('gd')) {
            Log::warning('ImageHelper: PHP GD extension is not loaded. Image compression and resizing skipped.');
            return;
        }

        try {
            $path = $file->getRealPath();
            if (!$path || !file_exists($path)) {
                return;
            }

            // Get image info
            $info = getimagesize($path);
            if (!$info) {
                return; // Not a readable image or not supported format
            }

            list($width, $height, $type) = $info;

            $newWidth = $width;
            $newHeight = $height;

            if ($width > $maxWidth || $height > $maxHeight) {
                $ratio = min($maxWidth / $width, $maxHeight / $height);
                $newWidth = (int) round($width * $ratio);
                $newHeight = (int) round($height * $ratio);
            }

            // Load image depending on type
            $image = null;
            switch ($type) {
                case IMAGETYPE_JPEG:
                    $image = @imagecreatefromjpeg($path);
                    break;
                case IMAGETYPE_PNG:
                    $image = @imagecreatefrompng($path);
                    break;
                case IMAGETYPE_GIF:
                    $image = @imagecreatefromgif($path);
                    break;
                case IMAGETYPE_WEBP:
                    $image = @imagecreatefromwebp($path);
                    break;
            }

            if (!$image) {
                Log::warning("ImageHelper: Failed to create image resource from file type {$type} at path: {$path}");
                return;
            }

            // Handle transparency for PNG and GIF
            $newImage = imagecreatetruecolor($newWidth, $newHeight);
            if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);
                $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
                imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
            }

            // Resample
            imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

            // Save image back to the temporary path (overwriting it)
            switch ($type) {
                case IMAGETYPE_JPEG:
                    imagejpeg($newImage, $path, 80); // quality = 80
                    break;
                case IMAGETYPE_PNG:
                    imagepng($newImage, $path, 7); // compression level 7 (0-9)
                    break;
                case IMAGETYPE_GIF:
                    imagegif($newImage, $path);
                    break;
                case IMAGETYPE_WEBP:
                    imagewebp($newImage, $path, 80); // quality = 80
                    break;
            }

            imagedestroy($image);
            imagedestroy($newImage);

        } catch (\Throwable $e) {
            Log::error('ImageHelper: Exception occurred during image compression/resizing: ' . $e->getMessage(), [
                'exception' => $e
            ]);
        }
    }
}
