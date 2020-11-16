<?php

namespace BagistoPackages\Shop\Http\Controllers;

use BagistoPackages\Shop\Facades\Cart;
use BagistoPackages\Shop\Repositories\ProductRepository;
use BagistoPackages\Shop\Repositories\WishlistRepository;

class WishlistController extends Controller
{
    /**
     * ProductRepository object
     *
     * @var WishlistRepository
     */
    protected $wishlistRepository;

    /**
     * WishlistRepository object
     *
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * Create a new controller instance.
     *
     * @param WishlistRepository $wishlistRepository
     * @param ProductRepository $productRepository
     * @return void
     */
    public function __construct(WishlistRepository $wishlistRepository, ProductRepository $productRepository)
    {
        $this->middleware('customer');

        $this->wishlistRepository = $wishlistRepository;
        $this->productRepository = $productRepository;

        parent::__construct();
    }

    /**
     * Displays the listing resources if the customer having items in wishlist.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
     */
    public function index()
    {
        $wishlistItems = $this->wishlistRepository->getCustomerWhishlist();

        return view('shop::customers.account.wishlist.wishlist')->with('items', $wishlistItems);
    }

    /**
     * Function to add item to the wishlist.
     *
     * @param int $itemId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function add($itemId)
    {
        $product = $this->productRepository->findOneByField('id', $itemId);

        if (!$product->status)
            return redirect()->back();

        $data = [
            'channel_id' => core()->getCurrentChannel()->id,
            'product_id' => $itemId,
            'customer_id' => auth()->guard('customer')->user()->id,
        ];

        $checked = $this->wishlistRepository->findWhere([
            'channel_id' => core()->getCurrentChannel()->id,
            'product_id' => $itemId,
            'customer_id' => auth()->guard('customer')->user()->id,
        ]);

        //accidental case if some one adds id of the product in the anchor tag amd gives id of a variant.
        if ($product->parent_id != null) {
            $product = $this->productRepository->findOneByField('id', $product->parent_id);
            $data['product_id'] = $product->id;
        }

        if ($checked->isEmpty()) {
            if ($this->wishlistRepository->create($data)) {
                session()->flash('success', trans('shop::app.wishlist.success'));

                return redirect()->back();
            } else {
                session()->flash('error', trans('shop::app.wishlist.failure'));

                return redirect()->back();
            }
        } else {
            $this->wishlistRepository->findOneWhere([
                'product_id' => $data['product_id']
            ])->delete();

            session()->flash('success', trans('shop::app.wishlist.removed'));

            return redirect()->back();
        }
    }

    /**
     * Function to remove item to the wishlist.
     *
     * @param int $itemId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove($itemId)
    {
        $customerWishlistItems = auth()->guard('customer')->user()->wishlist_items;

        foreach ($customerWishlistItems as $customerWishlistItem) {
            if ($itemId == $customerWishlistItem->id) {
                $this->wishlistRepository->delete($itemId);

                session()->flash('success', trans('shop::app.wishlist.removed'));

                return redirect()->back();
            }
        }

        session()->flash('error', trans('shop::app.wishlist.remove-fail'));

        return redirect()->back();
    }

    /**
     * Function to move item from wishlist to cart.
     *
     * @param int $itemId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function move($itemId)
    {
        $wishlistItem = $this->wishlistRepository->findOneWhere([
            'id' => $itemId,
            'customer_id' => auth()->guard('customer')->user()->id,
        ]);

        if (!$wishlistItem) {
            abort(404);
        }

        try {
            $result = Cart::moveToCart($wishlistItem);

            if ($result) {
                session()->flash('success', trans('shop::app.customer.account.wishlist.moved'));
            } else {
                session()->flash('info', trans('shop::app.checkout.cart.integrity.missing_options'));

                return redirect()->route('shop.productOrCategory.index', $wishlistItem->product->url_key);
            }

            return redirect()->back();
        } catch (\Exception $e) {
            report($e);

            session()->flash('warning', $e->getMessage());

            return redirect()->route('shop.productOrCategory.index', $wishlistItem->product->url_key);
        }
    }

    /**
     * Function to remove all of the items items in the customer's wishlist
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeAll()
    {
        $wishlistItems = auth()->guard('customer')->user()->wishlist_items;

        if ($wishlistItems->count() > 0) {
            foreach ($wishlistItems as $wishlistItem) {
                $this->wishlistRepository->delete($wishlistItem->id);
            }
        }

        session()->flash('success', trans('shop::app.wishlist.remove-all-success'));

        return redirect()->back();
    }
}
