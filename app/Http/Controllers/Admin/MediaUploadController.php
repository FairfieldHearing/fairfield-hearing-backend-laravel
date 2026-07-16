<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MediaUploadController extends Controller
{
    public function upload(Request $request)
    {
        // Enforce same permissions as other admin features
        Gate::authorize('manage-blogs');

        $request->validate([
            'file' => 'required|image|max:8192',
        ]);

        try {
            $file = $request->file('file');
            
            // Upload helper compresses image (max 1000px width/height by default) and registers in media table
            $media = Media::upload($file, 'tinymce');

            return response()->json([
                'location' => $media->url,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
