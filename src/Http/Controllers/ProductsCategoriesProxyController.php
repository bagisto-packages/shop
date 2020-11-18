<?php


namespace BagistoPackages\Shop\Http\Controllers;

use Illuminate\Http\Request;
use BagistoPackages\Shop\Repositories\CategoryRepository;
use BagistoPackages\Shop\Repositories\ProductRepository;

class ProductsCategoriesProxyController extends Controller
{
    /**
     * CategoryRepository object
     *
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * ProductRepository object
     *
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * Create a new controller instance.
     *
     * @param CategoryRepository $categoryRepository
     * @param ProductRepository $productRepository
     *
     * @return void
     */
    public function __construct(CategoryRepository $categoryRepository, ProductRepository $productRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;

        parent::__construct();
    }

    /**
     * Show product or category view. If neither category nor product matches, abort with code 404.
     *
     * @param Request $request
     * @return \Exception|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $slugOrPath = trim($request->getPathInfo(), '/');

        if (preg_match('/^([a-z0-9-]+\/?)+$/', $slugOrPath)) {
            if ($category = $this->categoryRepository->findByPath($slugOrPath)) {
                return view('shop::products.index', compact('category'));
            }

            if ($product = $this->productRepository->findBySlug($slugOrPath)) {
                $customer = auth()->guard('customer')->user();

                return view('shop::products.view', compact('product', 'customer'));
            }

            abort(404);
        }

        return view('shop::home.index');
    }
}
