<?php

namespace BagistoPackages\Shop\Helpers;

use Illuminate\Support\Facades\Storage;
use BagistoPackages\Shop\Repositories\ProductRepository;

class ProductImage extends AbstractProduct
{
    /**
     * ProductRepository instance
     *
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * Create a new helper instance.
     *
     * @param ProductRepository $productRepository
     * @return void
     */
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Retrieve collection of gallery images
     *
     * @param \BagistoPackages\Shop\Contracts\Product|\BagistoPackages\Shop\Contracts\ProductFlat $product
     * @return array
     */
    public function getGalleryImages($product)
    {
        if (!$product) {
            return [];
        }

        $images = [];

        foreach ($product->images as $image) {
            if (!Storage::has($image->path)) {
                continue;
            }

            $images[] = [
                'small_image_url' => url('cache/small/' . $image->path),
                'medium_image_url' => url('cache/medium/' . $image->path),
                'large_image_url' => url('cache/large/' . $image->path),
                'original_image_url' => url('cache/original/' . $image->path),
            ];
        }

        if (!$product->parent_id && !count($images)) {
            $images[] = [
                'small_image_url' => asset('vendor/packages/ui/assets/images/product/small-product-placeholder.webp'),
                'medium_image_url' => asset('vendor/packages/ui/assets/images/product/meduim-product-placeholder.webp'),
                'large_image_url' => asset('vendor/packages/ui/assets/images/product/large-product-placeholder.webp'),
                'original_image_url' => asset('vendor/packages/ui/assets/images/product/large-product-placeholder.webp'),
            ];
        }

        return $images;
    }

    /**
     * Get product's base image
     *
     * @param \BagistoPackages\Shop\Contracts\Product|\BagistoPackages\Shop\Contracts\ProductFlat $product
     * @return array
     */
    public function getProductBaseImage($product)
    {
        $images = $product ? $product->images : null;

        if ($images && $images->count()) {
            $image = [
                'small_image_url' => url('cache/small/' . $images[0]->path),
                'medium_image_url' => url('cache/medium/' . $images[0]->path),
                'large_image_url' => url('cache/large/' . $images[0]->path),
                'original_image_url' => url('cache/original/' . $images[0]->path),
            ];
        } else {
            $image = [
                'small_image_url' => asset('vendor/packages/ui/assets/images/product/small-product-placeholder.webp'),
                'medium_image_url' => asset('vendor/packages/ui/assets/images/product/meduim-product-placeholder.webp'),
                'large_image_url' => asset('vendor/packages/ui/assets/images/product/large-product-placeholder.webp'),
                'original_image_url' => asset('vendor/packages/ui/assets/images/product/large-product-placeholder.webp'),
            ];
        }

        return $image;
    }

    /**
     * Get product variant image if available otherwise product base image
     *
     * @param \BagistoPackages\Shop\Contracts\Wishlist $item
     * @return array
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function getProductImage($item)
    {
        if ($item instanceof \BagistoPackages\Shop\Contracts\Wishlist) {
            if (isset($item->additional['selected_configurable_option'])) {
                $product = $this->productRepository->find($item->additional['selected_configurable_option']);
            } else {
                $product = $item->product;
            }
        } else {
            $product = $item->product;
        }

        return $this->getProductBaseImage($product);
    }
}
