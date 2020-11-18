<?php

namespace BagistoPackages\Shop\Http\Controllers;

use BagistoPackages\Shop\Repositories\ProductRepository;
use BagistoPackages\Shop\Repositories\ProductReviewRepository;

class ReviewController extends Controller
{
    /**
     * ProductRepository object
     *
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * ProductReviewRepository object
     *
     * @var ProductReviewRepository
     */
    protected $productReviewRepository;

    /**
     * Create a new controller instance.
     *
     * @param ProductRepository $productRepository
     * @param ProductReviewRepository $productReviewRepository
     * @return void
     */
    public function __construct(ProductRepository $productRepository, ProductReviewRepository $productReviewRepository)
    {
        $this->productRepository = $productRepository;
        $this->productReviewRepository = $productReviewRepository;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  string  $slug
     * @return \Exception|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
     */
    public function create($slug)
    {
        if (auth()->guard('customer')->check() || core()->getConfigData('catalog.products.review.guest_review')) {
            $product = $this->productRepository->findBySlugOrFail($slug);

            return view('shop::products.reviews.create', compact('product'));
        }

        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store($id)
    {
        $this->validate(request(), [
            'comment' => 'required',
            'rating'  => 'required|numeric|min:1|max:5',
            'title'   => 'required',
        ]);

        $data = request()->all();

        if (auth()->guard('customer')->user()) {
            $data['customer_id'] = auth()->guard('customer')->user()->id;
            $data['name'] = auth()->guard('customer')->user()->first_name . ' ' . auth()->guard('customer')->user()->last_name;
        }

        $data['status'] = 'pending';
        $data['product_id'] = $id;

        $this->productReviewRepository->create($data);

        session()->flash('success', trans('shop::app.response.submit-success', ['name' => 'Product Review']));

        return redirect()->route('shop.home.index');
    }

    /**
     * Display reviews of particular product.
     *
     * @param  string  $slug
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
     */
    public function show($slug)
    {
        $product = $this->productRepository->findBySlugOrFail($slug);

        return view('shop::products.reviews.index', compact('product'));
    }

    /**
     * Customer delete a reviews from their account
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $review = $this->productReviewRepository->findOneWhere([
            'id'          => $id,
            'customer_id' => auth()->guard('customer')->user()->id,
        ]);

        if (! $review) {
            abort(404);
        }

        $this->productReviewRepository->delete($id);

        session()->flash('success', trans('shop::app.response.delete-success', ['name' => 'Product Review']));

        return redirect()->route('shop.customer.reviews.index');
    }

    /**
     * Customer delete all reviews from their account
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteAll()
    {
        $reviews = auth()->guard('customer')->user()->all_reviews;

        if ($reviews->count() > 0) {
            foreach ($reviews as $review) {
                $this->productReviewRepository->delete($review->id);
            }
        }

        session()->flash('success', trans('shop::app.reviews.delete-all'));

        return redirect()->route('shop.customer.reviews.index');
    }
}
