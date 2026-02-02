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
    public function optimizeAndStoreImage($file, $path, $filename, $maxWidth = 1920)
    {
        // Standard Web Limit: 1920px (Full HD)
        // If user provided a larger limit, we respect it, but default to 1920 for "Standard Fasting"
        
        if (! extension_loaded('gd')) {
            $file->storeAs($path, $filename, 'public');
            return;
        }

        try {
            $sourcePath = $file->getPathname();
            $extension = strtolower($file->getClientOriginalExtension());
            list($width, $height) = getimagesize($sourcePath);

            // Calculate new dimensions
            if ($width > $maxWidth) {
                $newWidth = $maxWidth;
                $newHeight = (int) ($height * ($newWidth / $width));
            } else {
                $newWidth = $width;
                $newHeight = $height;
            }

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
                // Resample
                imagecopyresampled($image_p, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                
                $tempPath = tempnam(sys_get_temp_dir(), 'img_');
                
                // SAVE OPTIMIZED
                switch ($extension) {
                    case 'jpg':
                    case 'jpeg':
                        imageinterlace($image_p, true); // Progressive JPEG (Loads faster visually)
                        imagejpeg($image_p, $tempPath, 80); // Quality 80 (Standard Web Balance)
                        break;
                    case 'png':
                        // PNGs are often huge. If it doesn't have transparency, JPEG is better, 
                        // but we must keep extension. 
                        // Compress level 6 (0-9).
                        imagepng($image_p, $tempPath, 6); 
                        break;
                    case 'gif':
                        imagegif($image_p, $tempPath);
                        break;
                    case 'webp':
                        imagewebp($image_p, $tempPath, 80); // Quality 80
                        break;
                }

                Storage::disk('public')->putFileAs($path, new File($tempPath), $filename);
                
                imagedestroy($image_p);
                imagedestroy($image);
                @unlink($tempPath);
                return;
            }
        } catch (\Exception $e) {
            Log::warning('Image optimization failed, falling back to original: ' . $e->getMessage());
        }

        // Fallback
        $file->storeAs($path, $filename, 'public');
    }
}
