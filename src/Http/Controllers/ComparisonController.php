<?php

namespace BagistoPackages\Shop\Http\Controllers;

use BagistoPackages\Shop\Repositories\ProductRepository;
use BagistoPackages\Shop\Repositories\ProductFlatRepository;
use BagistoPackages\Shop\Repositories\CustomerCompareProductRepository as CustomerCompareProductRepository;

class ComparisonController extends Controller
{
    /**
     * @var CustomerCompareProductRepository
     */
    protected $compareProductRepository;

    /**
     * @var ProductFlatRepository
     */
    protected $productFlatRepository;
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * ComparisonController constructor.
     *
     * @param CustomerCompareProductRepository $compareProductRepository
     * @param ProductFlatRepository $productFlatRepository
     * @param ProductRepository $productRepository
     */
    public function __construct(
        CustomerCompareProductRepository $compareProductRepository,
        ProductFlatRepository $productFlatRepository,
        ProductRepository $productRepository
    )
    {
        $this->compareProductRepository = $compareProductRepository;
        $this->productFlatRepository = $productFlatRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * function for customers to get products in comparison.
     *
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function getComparisonList()
    {
        if (request()->get('data')) {
            $productSlugs = null;

            $productCollection = [];

            if (auth()->guard('customer')->user()) {
                $productCollection = $this->compareProductRepository
                    ->leftJoin(
                        'product_flat',
                        'customer_compare_products.product_flat_id',
                        'product_flat.id'
                    )
                    ->where('customer_id', auth()->guard('customer')->user()->id)
                    ->get()
                    ->toArray();

                $items = [];

                foreach ($productCollection as $index => $customerCompare) {
                    array_push($items, $customerCompare['id']);
                }

                $items = implode('&', $items);
                $productCollection = $this->fetchProductCollection($items);

            } else {
                // for product details
                if ($items = request()->get('items')) {
                    $productCollection = $this->fetchProductCollection($items);
                }
            }

            $response = [
                'status' => 'success',
                'products' => $productCollection,
            ];
        } else {
            $response = view('shop::guest.compare.index');
        }

        return $response;
    }

    public function getCustomerComparisonList()
    {
        if (request()->get('data')) {
            $productSlugs = null;

            $productCollection = [];

            if (auth()->guard('customer')->user()) {
                $productCollection = $this->compareProductRepository
                    ->leftJoin(
                        'product_flat',
                        'customer_compare_products.product_flat_id',
                        'product_flat.id'
                    )
                    ->where('customer_id', auth()->guard('customer')->user()->id)
                    ->get()
                    ->toArray();

                $items = [];

                foreach ($productCollection as $index => $customerCompare) {
                    array_push($items, $customerCompare['id']);
                }

                $items = implode('&', $items);
                $productCollection = $this->fetchProductCollection($items);

            } else {
                // for product details
                if ($items = request()->get('items')) {
                    $productCollection = $this->fetchProductCollection($items);
                }
            }

            $response = [
                'status' => 'success',
                'products' => $productCollection,
            ];
        } else {
            $response = view('shop::customers.account.compare.index');
        }

        return $response;
    }

    /**
     * function for customers to add product in comparison.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function addCompareProduct()
    {
        $productId = request()->get('productId');

        $customerId = auth()->guard('customer')->user()->id;

        $compareProduct = $this->compareProductRepository->findOneByField([
            'customer_id' => $customerId,
            'product_flat_id' => $productId,
        ]);

        if (!$compareProduct) {
            // insert new row

            $productFlatRepository = app('\BagistoPackages\Shop\Models\ProductFlat');

            $productFlat = $productFlatRepository
                ->where('id', $productId)
                ->orWhere('parent_id', $productId)
                ->orWhere('id', $productId)
                ->get()
                ->first();

            if ($productFlat) {
                $productId = $productFlat->id;

                $this->compareProductRepository->create([
                    'customer_id' => $customerId,
                    'product_flat_id' => $productId,
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => trans('shop::app.customer.compare.added'),
                'label' => trans('shop::app.shop.general.alert.success'),
            ], 201);
        } else {
            return response()->json([
                'status' => 'success',
                'label' => trans('shop::app.shop.general.alert.success'),
                'message' => trans('shop::app.customer.compare.already_added'),
            ], 200);
        }
    }

    /**
     * function for customers to delete product in comparison.
     *
     * @return array
     */
    public function deleteComparisonProduct()
    {
        // either delete all or individual
        if (request()->get('productId') == 'all') {
            // delete all
            $customerId = auth()->guard('customer')->user()->id;
            $this->compareProductRepository->deleteWhere([
                'customer_id' => auth()->guard('customer')->user()->id,
            ]);
            $message = trans('shop::app.customer.compare.removed-all');
        } else {
            // delete individual
            $this->compareProductRepository->deleteWhere([
                'product_flat_id' => request()->get('productId'),
                'customer_id' => auth()->guard('customer')->user()->id,
            ]);
            $message = trans('shop::app.customer.compare.removed');
        }

        return [
            'status' => 'success',
            'message' => $message,
            'label' => trans('shop::app.shop.general.alert.success'),
        ];
    }

    public function fetchProductCollection($items, $moveToCart = false, $separator = '&')
    {
        $productCollection = [];
        $productIds = explode($separator, $items);

        foreach ($productIds as $productId) {
            // @TODO:- query only once insted of 2
            $productFlat = $this->productFlatRepository->findOneWhere(['id' => $productId]);

            if ($productFlat) {
                $product = $this->productRepository->findOneWhere(['id' => $productFlat->product_id]);

                if ($product) {
                    $formattedProduct = $this->formatProduct($productFlat, false, [
                        'moveToCart' => $moveToCart,
                        'btnText' => $moveToCart ? trans('shop::app.customer.account.wishlist.move-to-cart') : null,
                    ]);

                    $productMetaDetails = [];
                    $productMetaDetails['slug'] = $product->url_key;
                    $productMetaDetails['product_image'] = $formattedProduct['image'];
                    $productMetaDetails['priceHTML'] = $formattedProduct['priceHTML'];
                    $productMetaDetails['new'] = $formattedProduct['new'];
                    $productMetaDetails['addToCartHtml'] = $formattedProduct['addToCartHtml'];
                    $productMetaDetails['galleryImages'] = $formattedProduct['galleryImages'];
                    $productMetaDetails['defaultAddToCart'] = $formattedProduct['defaultAddToCart'];

                    $product = array_merge($productFlat->toArray(), $productMetaDetails);

                    array_push($productCollection, $product);
                }
            }
        }

        return $productCollection;
    }

    protected function formatProduct($product, $list = false, $metaInformation = [])
    {
        $reviewHelper = app('BagistoPackages\Shop\Helpers\Review');
        $productImageHelper = app('BagistoPackages\Shop\Helpers\ProductImage');

        $totalReviews = $reviewHelper->getTotalReviews($product);

        $avgRatings = ceil($reviewHelper->getAverageRating($product));

        $galleryImages = $productImageHelper->getGalleryImages($product);
        $productImage = $productImageHelper->getProductBaseImage($product)['medium_image_url'];

        $largeProductImageName = "large-product-placeholder.png";
        $mediumProductImageName = "meduim-product-placeholder.png";

        if (strpos($productImage, $mediumProductImageName) > -1) {
            $productImageNameCollection = explode('/', $productImage);
            $productImageName = $productImageNameCollection[sizeof($productImageNameCollection) - 1];

            if ($productImageName == $mediumProductImageName) {
                $productImage = str_replace($mediumProductImageName, $largeProductImageName, $productImage);
            }
        }

        $priceHTML = view('shop::products.price', ['product' => $product])->render();

        $isProductNew = ($product->new && !strpos($priceHTML, 'sticker sale') > 0) ? __('shop::app.products.new') : false;

        return [
            'priceHTML' => $priceHTML,
            'avgRating' => $avgRatings,
            'totalReviews' => $totalReviews,
            'image' => $productImage,
            'new' => $isProductNew,
            'galleryImages' => $galleryImages,
            'name' => $product->name,
            'slug' => $product->url_key,
            'description' => $product->description,
            'shortDescription' => $product->short_description,
            'firstReviewText' => trans('velocity::app.products.be-first-review'),
            'defaultAddToCart' => view('shop::products.add-buttons', ['product' => $product])->render(),
            'addToCartHtml' => view('shop::products.add-to-cart', [
                'product' => $product,
                'addWishlistClass' => !(isset($list) && $list) ? '' : '',
                'showCompare' => core()->getConfigData('general.content.shop.compare_option') == "1",
                'btnText' => (isset($metaInformation['btnText']) && $metaInformation['btnText']) ? $metaInformation['btnText'] : null,
                'moveToCart' => (isset($metaInformation['moveToCart']) && $metaInformation['moveToCart']) ? $metaInformation['moveToCart'] : null,
                'addToCartBtnClass' => !(isset($list) && $list) ? 'small-padding' : '',
            ])->render(),
        ];
    }
}
