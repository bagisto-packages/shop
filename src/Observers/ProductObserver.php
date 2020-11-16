<?php

namespace BagistoPackages\Shop\Observers;

use Illuminate\Support\Facades\Storage;

class ProductObserver
{
    /**
     * Handle the Product "deleted" event.
     *
     * @param \BagistoPackages\Shop\Contracts\Product $product
     * @return void
     */
    public function deleted($product)
    {
        Storage::deleteDirectory('product/' . $product->id);
    }
}
