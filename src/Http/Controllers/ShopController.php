<?php

namespace BagistoPackages\Shop\Http\Controllers;

use BagistoPackages\Shop\Repositories\CustomerCompareProductRepository;
use BagistoPackages\Shop\Repositories\WishlistRepository;

class ShopController extends Controller
{
    /**
     * @var WishlistRepository
     */
    protected $wishlistRepository;

    /**
     * @var CustomerCompareProductRepository
     */
    protected $compareProductRepository;

    public function __construct(
        WishlistRepository $wishlistRepository,
        CustomerCompareProductRepository $compareProductRepository
    )
    {
        $this->wishlistRepository = $wishlistRepository;
        $this->compareProductRepository = $compareProductRepository;
    }

    public function getItemsCount()
    {
        if ($customer = auth()->guard('customer')->user()) {
            $wishlistItemsCount = $this->wishlistRepository->count([
                'customer_id' => $customer->id,
                'channel_id' => core()->getCurrentChannel()->id,
            ]);

            $comparedItemsCount = $this->compareProductRepository->count([
                'customer_id' => $customer->id,
            ]);

            $response = [
                'status' => true,
                'compareProductsCount' => $comparedItemsCount,
                'wishlistedProductsCount' => $wishlistItemsCount,
            ];
        }

        return response()->json($response ?? ['status' => false]);
    }
}
