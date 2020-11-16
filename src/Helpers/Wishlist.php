<?php

namespace BagistoPackages\Shop\Helpers;

use BagistoPackages\Shop\Repositories\WishlistRepository;

class Wishlist
{
    /**
     * WishlistRepository object
     *
     * @var WishlistRepository
     */
    protected $wishlistRepository;

    /**
     * Create a new controller instance.
     *
     * @param WishlistRepository $wishlistRepository
     */
    public function __construct(WishlistRepository $wishlistRepository)
    {
        $this->wishlistRepository = $wishlistRepository;
    }

    /**
     * Returns wishlist products for current customer.
     *
     * @param \BagistoPackages\Shop\Contracts\Product $product
     * @return boolean
     */
    public function getWishlistProduct($product)
    {
        $wishlist = false;

        if (auth()->guard('customer')->user()) {
            $wishlist = $this->wishlistRepository->findOneWhere([
                'channel_id' => core()->getCurrentChannel()->id,
                'product_id' => $product->product_id,
                'customer_id' => auth()->guard('customer')->user()->id,
            ]);
        }

        if ($wishlist) {
            return true;
        }

        return false;
    }
}
