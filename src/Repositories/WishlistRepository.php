<?php

namespace BagistoPackages\Shop\Repositories;

use BagistoPackages\Shop\Eloquent\Repository;

class WishlistRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */

    function model()
    {
        return 'BagistoPackages\Shop\Contracts\Wishlist';
    }

    /**
     * @param array $data
     * @return \BagistoPackages\Shop\Contracts\Wishlist
     */
    public function create(array $data)
    {
        $wishlist = $this->model->create($data);

        return $wishlist;
    }

    /**
     * @param array $data
     * @param int $id
     * @param string $attribute
     * @return \BagistoPackages\Shop\Contracts\Wishlist
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function update(array $data, $id, $attribute = "id")
    {
        $wishlist = $this->find($id);

        $wishlist->update($data);

        return $wishlist;
    }

    /**
     * To retrieve products with wishlist for a listing resource.
     *
     * @param int $id
     * @return \BagistoPackages\Shop\Contracts\Wishlist
     */
    public function getItemsWithProducts($id)
    {
        return $this->model->find($id)->item_wishlist;
    }

    /**
     * get customer wishlist Items.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getCustomerWhishlist()
    {
        return $this->model->where([
            'channel_id' => core()->getCurrentChannel()->id,
            'customer_id' => auth()->guard('customer')->user()->id,
        ])->paginate(5);
    }
}
