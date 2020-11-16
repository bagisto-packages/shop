<?php

namespace BagistoPackages\Shop\Repositories;

use Illuminate\Support\Str;
use BagistoPackages\Shop\Eloquent\Repository;

class ProductCustomerGroupPriceRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'BagistoPackages\Shop\Contracts\ProductCustomerGroupPrice';
    }

    /**
     * @param array $data
     * @param \BagistoPackages\Shop\Contracts\Product $product
     * @return void
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function saveCustomerGroupPrices(array $data, $product)
    {
        $previousCustomerGroupPriceIds = $product->customer_group_prices()->pluck('id');

        if (isset($data['customer_group_prices'])) {
            foreach ($data['customer_group_prices'] as $customerGroupPriceId => $row) {
                $row['customer_group_id'] = $row['customer_group_id'] == '' ? null : $row['customer_group_id'];

                if (Str::contains($customerGroupPriceId, 'customer_group_price_')) {
                    $this->create(array_merge([
                        'product_id' => $product->id,
                    ], $row));
                } else {
                    if (is_numeric($index = $previousCustomerGroupPriceIds->search($customerGroupPriceId))) {
                        $previousCustomerGroupPriceIds->forget($index);
                    }

                    $this->update($row, $customerGroupPriceId);
                }
            }
        }

        foreach ($previousCustomerGroupPriceIds as $customerGroupPriceId) {
            $this->delete($customerGroupPriceId);
        }
    }
}
