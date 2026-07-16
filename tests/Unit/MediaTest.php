<?php

use App\Models\Media;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\Manufacturer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(\Tests\TestCase::class, RefreshDatabase::class);

test('it creates a media record and can upload an image', function () {
    Storage::fake('public');

    // Create a dummy image
    $file = UploadedFile::fake()->image('avatar.jpg', 1200, 1200);

    $media = Media::upload($file, 'test_folder');

    expect($media)->toBeInstanceOf(Media::class);
    expect($media->filename)->toBe(basename($media->filepath));
    expect($media->original_filename)->toBe('avatar.jpg');
    expect($media->folder)->toBe('test_folder');
    
    // Check if compression occurred (max size is 1000)
    expect($media->width)->toBeLessThanOrEqual(1000);
    expect($media->height)->toBeLessThanOrEqual(1000);
    
    Storage::disk('public')->assertExists($media->filepath);
});

test('it tracks media usage across models', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('test.png', 100, 100);
    $media = Media::upload($file, 'general');

    // Usage should be empty initially
    expect($media->getUsageInfo())->toBeEmpty();

    // Create blog category linked to it
    $category = BlogCategory::create([
        'title' => 'Test Category',
        'slug' => 'test-category',
        'image' => $media->filepath,
        'image_media_id' => $media->id,
    ]);

    // Create manufacturer linked to it
    $manufacturer = Manufacturer::create([
        'name' => 'Test Manufacturer',
        'logo_path' => $media->filepath,
        'logo_media_id' => $media->id,
        'is_active' => true,
        'show_on_homepage' => true,
    ]);

    // Create blog post linked to it
    $post = BlogPost::create([
        'blog_category_id' => $category->id,
        'title' => 'Test Post',
        'slug' => 'test-post',
        'summary' => 'Summary',
        'featured_image' => $media->filepath,
        'featured_image_media_id' => $media->id,
        'content' => '<p>Some content</p>',
        'author_name' => 'Author',
    ]);

    // Usage should contain 3 entries
    $usage = $media->getUsageInfo();
    expect($usage)->toHaveCount(3);

    $types = collect($usage)->pluck('type')->toArray();
    expect($types)->toContain('Blog Post');
    expect($types)->toContain('Blog Category');
    expect($types)->toContain('Manufacturer');
});
