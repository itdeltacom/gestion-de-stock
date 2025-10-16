<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\ProductImage;
use App\Services\ImageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessProductImages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $productId;
    protected $featuredImagePath;
    protected $galleryImagePaths;

    public function __construct($productId, $featuredImagePath = null, $galleryImagePaths = [])
    {
        $this->productId = $productId;
        $this->featuredImagePath = $featuredImagePath;
        $this->galleryImagePaths = $galleryImagePaths;
    }

    public function handle(ImageService $imageService)
    {
        $product = Product::find($this->productId);

        if (!$product) {
            return;
        }

        // Process featured image
        if ($this->featuredImagePath && file_exists($this->featuredImagePath)) {
            try {
                $paths = $imageService->processFeaturedImage($this->featuredImagePath);
                $product->featured_image = $paths['original'];
                $product->save();

                // Delete temp file
                @unlink($this->featuredImagePath);
            } catch (\Exception $e) {
                \Log::error('Featured image processing failed: ' . $e->getMessage());
            }
        }

        // Process gallery images
        foreach ($this->galleryImagePaths as $index => $imagePath) {
            if (file_exists($imagePath)) {
                try {
                    $paths = $imageService->processGalleryImage($imagePath);
                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $paths['original'],
                        'sort_order' => $index,
                    ]);

                    // Delete temp file
                    @unlink($imagePath);
                } catch (\Exception $e) {
                    \Log::error('Gallery image processing failed: ' . $e->getMessage());
                }
            }
        }
    }
}