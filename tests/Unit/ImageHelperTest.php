<?php

use App\Helpers\ImageHelper;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

test('it resizes large images proportionally to a max of 1000px', function () {
    // 1. Create a large test image (2000x1200)
    $tempFile = tempnam(sys_get_temp_dir(), 'test_img_') . '.jpg';
    $img = imagecreatetruecolor(2000, 1200);
    imagejpeg($img, $tempFile);
    imagedestroy($img);

    // Create UploadedFile
    $uploadedFile = new UploadedFile(
        $tempFile,
        'test_img.jpg',
        'image/jpeg',
        null,
        true // test mode
    );

    // Verify initial size
    list($width, $height) = getimagesize($tempFile);
    expect($width)->toBe(2000);
    expect($height)->toBe(1200);

    // Run compression and resizing
    ImageHelper::compressAndResize($uploadedFile);

    // Verify new size
    list($newWidth, $newHeight) = getimagesize($tempFile);
    expect($newWidth)->toBe(1000);
    expect($newHeight)->toBe(600); // 1200 * (1000/2000)

    // Cleanup
    if (file_exists($tempFile)) {
        unlink($tempFile);
    }
});

test('it does not upscale small images but processes them', function () {
    // Create a small test image (500x300)
    $tempFile = tempnam(sys_get_temp_dir(), 'test_img_') . '.jpg';
    $img = imagecreatetruecolor(500, 300);
    imagejpeg($img, $tempFile);
    imagedestroy($img);

    $uploadedFile = new UploadedFile($tempFile, 'test_img.jpg', 'image/jpeg', null, true);

    ImageHelper::compressAndResize($uploadedFile);

    list($newWidth, $newHeight) = getimagesize($tempFile);
    expect($newWidth)->toBe(500);
    expect($newHeight)->toBe(300);

    if (file_exists($tempFile)) {
        unlink($tempFile);
    }
});

test('it handles non-image files or missing files gracefully without throwing errors', function () {
    // Create a text file mimicking an upload
    $tempFile = tempnam(sys_get_temp_dir(), 'test_txt_') . '.txt';
    file_put_contents($tempFile, 'Not an image');

    $uploadedFile = new UploadedFile($tempFile, 'test.txt', 'text/plain', null, true);

    // Call helper, should not throw exception
    expect(fn() => ImageHelper::compressAndResize($uploadedFile))->not->toThrow(Exception::class);

    if (file_exists($tempFile)) {
        unlink($tempFile);
    }
});
