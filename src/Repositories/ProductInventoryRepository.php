<?php

namespace BagistoPackages\Shop\Repositories;

use BagistoPackages\Shop\Eloquent\Repository;

class ProductInventoryRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'BagistoPackages\Shop\Contracts\ProductInventory';
    }

    /**
     * @param array $data
     * @param \BagistoPackages\Shop\Contracts\Product $product
     * @return void
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function saveInventories(array $data, $product)
    {
        if (isset($data['inventories'])) {
            foreach ($data['inventories'] as $inventorySourceId => $qty) {
                $qty = is_null($qty) ? 0 : $qty;

                $productInventory = $this->findOneWhere([
                    'product_id' => $product->id,
                    'inventory_source_id' => $inventorySourceId,
                    'vendor_id' => isset($data['vendor_id']) ? $data['vendor_id'] : 0,
                ]);

                if ($productInventory) {
                    $productInventory->qty = $qty;

                    $productInventory->save();
                } else {
                    $this->create([
                        'qty' => $qty,
                        'product_id' => $product->id,
                        'inventory_source_id' => $inventorySourceId,
                        'vendor_id' => isset($data['vendor_id']) ? $data['vendor_id'] : 0,
                    ]);
                }
            }
        }
    }
}
