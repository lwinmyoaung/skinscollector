<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class OptimizeImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'optimize:images {--path= : Specific path to optimize inside adminimages} {--force : Optimize even small images}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resize and optimize existing images in adminimages directory';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        ini_set('memory_limit', '1024M'); // Increase memory for large image processing

        if (!extension_loaded('gd')) {
            $this->error('GD extension is not loaded. Please enable it in php.ini to optimize images.');
            return 1;
        }

        $basePath = base_path('adminimages');
        
        if ($this->option('path')) {
            $basePath .= '/' . $this->option('path');
        }

        if (!File::exists($basePath)) {
            $this->error("Directory not found: $basePath");
            return 1;
        }

        $this->info("Scanning directory: $basePath");

        $files = File::allFiles($basePath);
        $count = 0;
        $totalSaved = 0;
        $errors = 0;

        $bar = $this->output->createProgressBar(count($files));
        $bar->start();

        foreach ($files as $file) {
            $extension = strtolower($file->getExtension());
            if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                $bar->advance();
                continue;
            }

            $realPath = $file->getRealPath();
            $size = $file->getSize(); // bytes

            // Skip if smaller than 100KB unless --force is used
            if ($size < 100 * 1024 && !$this->option('force')) {
                $bar->advance();
                continue;
            }

            try {
                $savedBytes = $this->optimizeImage($realPath, $extension);
                if ($savedBytes > 0) {
                    $totalSaved += $savedBytes;
                    $count++;
                }
            } catch (\Exception $e) {
                // $this->error("Error optimizing " . $file->getFilename() . ": " . $e->getMessage());
                $errors++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        
        $savedMb = round($totalSaved / 1024 / 1024, 2);
        $this->info("Optimization complete!");
        $this->info("Optimized $count images.");
        $this->info("Saved total: $savedMb MB.");
        if ($errors > 0) {
            $this->warn("Encountered $errors errors (likely corrupted files or unsupported formats).");
        }

        return 0;
    }

    private function optimizeImage($path, $extension)
    {
        $originalSize = filesize($path);
        
        // Detect REAL mime type to handle mismatched extensions
        $imageInfo = @getimagesize($path);
        if (!$imageInfo) {
            return 0; // Not a valid image
        }

        $mime = $imageInfo['mime'];
        $width = $imageInfo[0];
        $height = $imageInfo[1];

        // Max width standard
        $maxWidth = 1000;

        // Create resource based on REAL mime type
        $image = null;
        switch ($mime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($path);
                break;
            case 'image/png':
                $image = imagecreatefrompng($path);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($path);
                break;
            case 'image/webp':
                if (function_exists('imagecreatefromwebp')) {
                    $image = imagecreatefromwebp($path);
                }
                break;
        }

        if (!$image) {
            return 0;
        }

        // Calculate new dimensions
        if ($width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = (int) ($height * ($newWidth / $width));
        } else {
            $newWidth = $width;
            $newHeight = $height;
        }

        $image_p = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve transparency for PNG/WebP/GIF
        if ($mime == 'image/png' || $mime == 'image/webp' || $mime == 'image/gif') {
            imagealphablending($image_p, false);
            imagesavealpha($image_p, true);
        }

        // Resample
        imagecopyresampled($image_p, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Save based on FILE EXTENSION (to fix mismatches and keep links working)
        // If file is .jpg but was PNG, we convert to JPEG (saving space)
        // If file is .png but was JPEG, we convert to PNG (safe)
        
        $tempPath = $path . '.tmp';
        $saved = false;

        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                // Convert to JPEG (even if source was PNG)
                // Fill background with white if source had transparency
                if ($mime == 'image/png' || $mime == 'image/gif') {
                    $bg = imagecreatetruecolor($newWidth, $newHeight);
                    $white = imagecolorallocate($bg, 255, 255, 255);
                    imagefill($bg, 0, 0, $white);
                    imagecopyresampled($bg, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                    imagedestroy($image_p);
                    $image_p = $bg;
                }
                
                imageinterlace($image_p, true); // Progressive
                imagejpeg($image_p, $tempPath, 80);
                $saved = true;
                break;
                
            case 'png':
                imagepng($image_p, $tempPath, 6);
                $saved = true;
                break;
                
            case 'gif':
                imagegif($image_p, $tempPath);
                $saved = true;
                break;
                
            case 'webp':
                imagewebp($image_p, $tempPath, 80);
                $saved = true;
                break;
        }

        imagedestroy($image);
        imagedestroy($image_p);

        if ($saved && file_exists($tempPath)) {
            $newSize = filesize($tempPath);
            // Only replace if we saved space or if we fixed dimensions
            // Or if we fixed a mime-type mismatch (which we can't easily track here but assuming optimization is good)
            if ($newSize < $originalSize || $width > $maxWidth) {
                rename($tempPath, $path);
                return $originalSize - $newSize;
            } else {
                @unlink($tempPath);
            }
        }

        return 0;
    }
}
