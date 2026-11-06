<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Handles storing and replacing product images on the "public" disk.
 */
class ProductImageService
{
    public function store(UploadedFile $file): string
    {
        return $file->store(Product::IMAGE_DIR, 'public');
    }

    /**
     * Store the new image and remove the previous one. Returns the new path.
     */
    public function replace(Product $product, UploadedFile $file): string
    {
        $path = $this->store($file);

        if ($product->image && $product->image !== $path) {
            Storage::disk('public')->delete($product->image);
        }

        return $path;
    }
}
