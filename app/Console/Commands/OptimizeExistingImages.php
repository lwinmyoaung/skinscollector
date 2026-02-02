<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class OptimizeExistingImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:optimize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize and resize all existing images in adminimages directory';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $directory = base_path('adminimages');
        
        if (!File::exists($directory)) {
            $this->error("Directory not found: $directory");
            return 1;
        }

        $this->info("Scanning directory: $directory");

        $files = File::allFiles($directory);
        $count = 0;
        $optimized = 0;
        $skipped = 0;
        $errors = 0;

        $maxWidth = 1000;

        foreach ($files as $file) {
            $extension = strtolower($file->getExtension());
            if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                continue;
            }

            $count++;
            $filePath = $file->getPathname();
            
            try {
                if (!extension_loaded('gd')) {
                    $this->error("GD extension not loaded!");
                    return 1;
                }

                list($width, $height) = getimagesize($filePath);

                if ($width > $maxWidth) {
                    $this->line("Optimizing: " . $file->getFilename() . " ($width px)");
                    
                    $newWidth = $maxWidth;
                    $newHeight = (int) ($height * ($newWidth / $width));
                    
                    $image_p = imagecreatetruecolor($newWidth, $newHeight);
                    $image = null;

                    switch ($extension) {
                        case 'jpg':
                        case 'jpeg':
                            $image = imagecreatefromjpeg($filePath);
                            break;
                        case 'png':
                            $image = imagecreatefrompng($filePath);
                            imagealphablending($image_p, false);
                            imagesavealpha($image_p, true);
                            break;
                        case 'gif':
                            $image = imagecreatefromgif($filePath);
                            break;
                        case 'webp':
                            if (function_exists('imagecreatefromwebp')) {
                                $image = imagecreatefromwebp($filePath);
                            }
                            break;
                    }

                    if ($image) {
                        imagecopyresampled($image_p, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                        
                        // Overwrite original file
                        switch ($extension) {
                            case 'jpg':
                            case 'jpeg':
                                imagejpeg($image_p, $filePath, 85);
                                break;
                            case 'png':
                                imagepng($image_p, $filePath, 8);
                                break;
                            case 'gif':
                                imagegif($image_p, $filePath);
                                break;
                            case 'webp':
                                imagewebp($image_p, $filePath, 85);
                                break;
                        }

                        imagedestroy($image_p);
                        imagedestroy($image);
                        
                        $optimized++;
                    } else {
                        $skipped++;
                    }
                } else {
                    $skipped++;
                }
            } catch (\Exception $e) {
                $this->error("Failed to optimize " . $file->getFilename() . ": " . $e->getMessage());
                $errors++;
            }
        }

        $this->info("--------------------------------");
        $this->info("Total scanned: $count");
        $this->info("Optimized: $optimized");
        $this->info("Skipped (already small): $skipped");
        $this->info("Errors: $errors");
        $this->info("--------------------------------");
        $this->info("Done!");

        return 0;
    }
}
