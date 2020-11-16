<?php

namespace BagistoPackages\Shop\Repositories;

use BagistoPackages\Shop\Eloquent\Repository;
use Illuminate\Support\Str;

class ProductGroupedProductRepository extends Repository
{
    public function model()
    {
        return 'BagistoPackages\Shop\Contracts\ProductGroupedProduct';
    }

    /**
     * @param array $data
     * @param \BagistoPackages\Shop\Contracts\Product $product
     * @return void
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function saveGroupedProducts($data, $product)
    {
        $previousGroupedProductIds = $product->grouped_products()->pluck('id');

        if (isset($data['links'])) {
            foreach ($data['links'] as $linkId => $linkInputs) {
                if (Str::contains($linkId, 'link_')) {
                    $this->create(array_merge([
                        'product_id' => $product->id,
                    ], $linkInputs));
                } else {
                    if (is_numeric($index = $previousGroupedProductIds->search($linkId))) {
                        $previousGroupedProductIds->forget($index);
                    }

                    $this->update($linkInputs, $linkId);
                }
            }
        }

        foreach ($previousGroupedProductIds as $previousGroupedProductId) {
            $this->delete($previousGroupedProductId);
        }
    }
}
