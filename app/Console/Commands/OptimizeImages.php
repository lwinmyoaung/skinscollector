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
    protected $signature = 'optimize:images {--path= : Specific path to optimize inside adminimages}';

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

        $bar = $this->output->createProgressBar(count($files));
        $bar->start();

        foreach ($files as $file) {
            $extension = strtolower($file->getExtension());
            if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) {
                $bar->advance();
                continue;
            }

            $size = $file->getSize(); // bytes
            // Skip if smaller than 300KB
            if ($size < 300 * 1024) {
                $bar->advance();
                continue;
            }

            try {
                $this->optimizeImage($file->getRealPath(), $extension);
                $newSize = filesize($file->getRealPath());
                $saved = $size - $newSize;
                $totalSaved += $saved;
                $count++;
            } catch (\Exception $e) {
                // Log error but continue
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        
        $savedMb = round($totalSaved / 1024 / 1024, 2);
        $this->info("Optimization complete!");
        $this->info("Optimized $count images.");
        $this->info("Saved total: $savedMb MB.");

        return 0;
    }

    private function optimizeImage($path, $extension)
    {
        list($width, $height) = getimagesize($path);
        
        // Target max width
        $maxWidth = 1200;
        
        $newWidth = $width;
        $newHeight = $height;

        if ($width > $maxWidth) {
            $ratio = $maxWidth / $width;
            $newWidth = $maxWidth;
            $newHeight = $height * $ratio;
        }

        $src = null;
        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                $src = \imagecreatefromjpeg($path);
                break;
            case 'png':
                $src = \imagecreatefrompng($path);
                break;
            case 'webp':
                $src = \imagecreatefromwebp($path);
                break;
        }

        if (!$src) return;

        $dst = \imagecreatetruecolor($newWidth, $newHeight);

        // Handle transparency for PNG/WebP
        if ($extension == 'png' || $extension == 'webp') {
            \imagecolortransparent($dst, \imagecolorallocatealpha($dst, 0, 0, 0, 127));
            \imagealphablending($dst, false);
            \imagesavealpha($dst, true);
        }

        \imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Save
        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                \imagejpeg($dst, $path, 80); // 80% quality
                break;
            case 'png':
                \imagepng($dst, $path, 8); 
                break;
            case 'webp':
                \imagewebp($dst, $path, 80);
                break;
        }

        \imagedestroy($src);
        \imagedestroy($dst);
    }
}
