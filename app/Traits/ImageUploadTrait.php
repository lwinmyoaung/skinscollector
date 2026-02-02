<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

trait ImageUploadTrait
{
    /**
     * Store an image directly without optimization.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $path
     * @param string $filename
     * @param int $maxWidth (Deprecated/Unused)
     * @return void
     */
    public function optimizeAndStoreImage($file, $path, $filename, $maxWidth = 1000)
    {
        // Direct save to folder without resizing/optimization
        $file->storeAs($path, $filename, 'public');
    }
}
