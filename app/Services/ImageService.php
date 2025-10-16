<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Str;

class ImageService
{
    protected $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Process and store image with optimized format
     * Quality: 85 is a good balance between quality and file size
     */
    public function processAndStore($file, $directory = 'products', $sizes = [], $quality = 85)
    {
        $filename = Str::random(40);
        $paths = [];

        try {
            // Load the image once
            $image = $this->manager->read($file);

            // Generate original size
            $originalPath = $this->saveOptimized($image, $directory, $filename . '_original', $quality);
            $paths['original'] = $originalPath;

            // Generate additional sizes if specified
            foreach ($sizes as $sizeName => $maxDimension) {
                $resizedImage = clone $image;

                // Resize maintaining aspect ratio - much faster than before
                $resizedImage->scale(width: $maxDimension, height: $maxDimension);

                $sizePath = $this->saveOptimized($resizedImage, $directory, $filename . '_' . $sizeName, $quality);
                $paths[$sizeName] = $sizePath;
            }

            return $paths;

        } catch (\Exception $e) {
            \Log::error('Image processing failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Save image in the best available format (WebP > JPEG)
     * This is MUCH faster than AVIF conversion
     */
    protected function saveOptimized($image, $directory, $filename, $quality = 85)
    {
        try {
            // Try WebP first (fast and great compression)
            if (function_exists('imagewebp')) {
                $extension = 'webp';
                $encoded = $image->toWebp($quality);
            }
            // Fallback to JPEG (universal support)
            else {
                $extension = 'jpg';
                $encoded = $image->toJpeg($quality);
            }

            $storagePath = $directory . '/' . $filename . '.' . $extension;
            Storage::disk('public')->put($storagePath, $encoded);

            return $storagePath;

        } catch (\Exception $e) {
            \Log::error('Image save failed: ' . $e->getMessage());

            // Last resort: save as JPEG
            $storagePath = $directory . '/' . $filename . '.jpg';
            $encoded = $image->toJpeg($quality);
            Storage::disk('public')->put($storagePath, $encoded);

            return $storagePath;
        }
    }

    /**
     * Process featured image with optimized sizes
     * Reduced number of sizes for faster processing
     */
    public function processFeaturedImage($file, $quality = 85)
    {
        return $this->processAndStore($file, 'products/featured', [
            'thumbnail' => 150,
            'medium' => 600,
            'large' => 1200
        ], $quality);
    }

    /**
     * Process gallery image with optimized sizes
     * Reduced number of sizes for faster processing
     */
    public function processGalleryImage($file, $quality = 85)
    {
        return $this->processAndStore($file, 'products/gallery', [
            'thumbnail' => 150,
            'medium' => 800,
            'large' => 1600
        ], $quality);
    }

    /**
     * Delete image and all its versions
     */
    public function deleteImage($path)
    {
        if (!$path) {
            return;
        }

        // Delete the main image
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        // Try to delete related sizes
        $pathInfo = pathinfo($path);
        $directory = $pathInfo['dirname'];
        $filename = $pathInfo['filename'];

        // Remove size suffix if exists
        $baseFilename = preg_replace('/_(?:original|thumbnail|small|medium|large|xlarge)$/', '', $filename);

        // Get all files with the same base name
        try {
            $files = Storage::disk('public')->files($directory);
            foreach ($files as $file) {
                $fileInfo = pathinfo($file);
                if (Str::startsWith($fileInfo['filename'], $baseFilename)) {
                    Storage::disk('public')->delete($file);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Image deletion failed: ' . $e->getMessage());
        }
    }

    /**
     * Get optimized image URL with size
     */
    /**
     * Get optimized image URL with size
     */
    public function getImageUrl($path, $size = 'original')
    {
        if (!$path) {
            return asset('assets/img/no-image.png');
        }

        try {
            // Get path info safely
            $pathInfo = pathinfo($path);

            // Check if extension exists
            if (!isset($pathInfo['extension']) || !isset($pathInfo['dirname']) || !isset($pathInfo['filename'])) {
                // If path is malformed, return placeholder
                if (Storage::disk('public')->exists($path)) {
                    return Storage::url($path);
                }
                return asset('assets/img/no-image.png');
            }

            // Remove size suffix if exists
            $baseFilename = preg_replace('/_(?:original|thumbnail|small|medium|large|xlarge)$/', '', $pathInfo['filename']);

            // Build optimized path
            $optimizedPath = $pathInfo['dirname'] . '/' . $baseFilename . '_' . $size . '.' . $pathInfo['extension'];

            // Check if optimized version exists
            if (Storage::disk('public')->exists($optimizedPath)) {
                return Storage::url($optimizedPath);
            }

            // Fallback to original if size not found
            if (Storage::disk('public')->exists($path)) {
                return Storage::url($path);
            }

            // Last resort: placeholder
            return asset('assets/img/no-image.png');

        } catch (\Exception $e) {
            \Log::error('Error getting image URL: ' . $e->getMessage() . ' - Path: ' . $path);
            return asset('assets/img/no-image.png');
        }
    }

    /**
     * Get all available sizes for an image
     */
    public function getImageSizes($path)
    {
        $sizes = [];

        if (!$path) {
            return $sizes;
        }

        $pathInfo = pathinfo($path);
        $directory = $pathInfo['dirname'];
        $baseFilename = preg_replace('/_(?:original|thumbnail|small|medium|large|xlarge)$/', '', $pathInfo['filename']);

        $sizeNames = ['thumbnail', 'small', 'medium', 'large', 'xlarge', 'original'];

        foreach ($sizeNames as $sizeName) {
            $sizePath = $directory . '/' . $baseFilename . '_' . $sizeName . '.' . $pathInfo['extension'];
            if (Storage::disk('public')->exists($sizePath)) {
                $sizes[$sizeName] = Storage::url($sizePath);
            }
        }

        return $sizes;
    }
}