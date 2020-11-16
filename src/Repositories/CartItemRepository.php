<?php

namespace BagistoPackages\Shop\Repositories;

use BagistoPackages\Shop\Eloquent\Repository;
use BagistoPackages\Shop\Contracts\CartItem;

class CartItemRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */

    function model()
    {
        return 'BagistoPackages\Shop\Contracts\CartItem';
    }

    /**
     * @param array $data
     * @param        $id
     * @param string $attribute
     *
     * @return CartItem|null
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function update(array $data, $id, $attribute = "id"): ?CartItem
    {
        $item = $this->find($id);

        if ($item) {
            $item->update($data);
        }

        return $item;
    }

    /**
     * @param int $cartItemId
     * @return int
     */
    public function getProduct($cartItemId)
    {
        return $this->model->find($cartItemId)->product->id;
    }
}
