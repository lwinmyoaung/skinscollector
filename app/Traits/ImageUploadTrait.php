<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

trait ImageUploadTrait
{
    /**
     * Optimize and store an image.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $path
     * @param string $filename
     * @param int $maxWidth
     * @return void
     */
    public function optimizeAndStoreImage($file, $path, $filename, $maxWidth = 1000)
    {
        if (! extension_loaded('gd')) {
            $file->storeAs($path, $filename, 'public');
            return;
        }

        try {
            $sourcePath = $file->getPathname();
            $extension = strtolower($file->getClientOriginalExtension());
            list($width, $height) = getimagesize($sourcePath);

            if ($width > $maxWidth) {
                $newWidth = $maxWidth;
                $newHeight = (int) ($height * ($newWidth / $width));
                
                $image_p = imagecreatetruecolor($newWidth, $newHeight);
                $image = null;

                switch ($extension) {
                    case 'jpg':
                    case 'jpeg':
                        $image = imagecreatefromjpeg($sourcePath);
                        break;
                    case 'png':
                        $image = imagecreatefrompng($sourcePath);
                        imagealphablending($image_p, false);
                        imagesavealpha($image_p, true);
                        break;
                    case 'gif':
                        $image = imagecreatefromgif($sourcePath);
                        break;
                    case 'webp':
                        if (function_exists('imagecreatefromwebp')) {
                            $image = imagecreatefromwebp($sourcePath);
                        }
                        break;
                }

                if ($image) {
                    imagecopyresampled($image_p, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                    
                    $tempPath = tempnam(sys_get_temp_dir(), 'img_');
                    
                    switch ($extension) {
                        case 'jpg':
                        case 'jpeg':
                            imagejpeg($image_p, $tempPath, 85);
                            break;
                        case 'png':
                            imagepng($image_p, $tempPath, 8);
                            break;
                        case 'gif':
                            imagegif($image_p, $tempPath);
                            break;
                        case 'webp':
                            imagewebp($image_p, $tempPath, 85);
                            break;
                    }

                    Storage::disk('public')->putFileAs($path, new File($tempPath), $filename);
                    
                    imagedestroy($image_p);
                    imagedestroy($image);
                    @unlink($tempPath);
                    return;
                }
            }
        } catch (\Exception $e) {
            Log::warning('Image optimization failed, falling back to original: ' . $e->getMessage());
        }

        // Fallback
        $file->storeAs($path, $filename, 'public');
    }
}
