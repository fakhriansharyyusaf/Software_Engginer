<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Services\ProductPlaceholderImage;
use Illuminate\Database\Seeder;

/**
 * Generates a branded placeholder image (via ProductPlaceholderImage) for
 * every seeded product that doesn't have a photo yet, so the catalog and
 * homepage look finished immediately — no internet access or copyrighted
 * images needed. Real Sellers can overwrite this any time by uploading a
 * real photo from the Seller dashboard.
 */
class ProductImageSeeder extends Seeder
{
    public function run(): void
    {
        Product::where(function ($q) {
            $q->whereNull('image')->orWhere('image', '');
        })->get()->each(function (Product $product) {
            $path = ProductPlaceholderImage::generateAndStore($product->id, $product->name);
            $product->update(['image' => $path]);
        });
    }
}
