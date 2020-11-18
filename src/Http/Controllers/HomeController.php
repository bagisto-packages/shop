<?php

namespace BagistoPackages\Shop\Http\Controllers;

use BagistoPackages\Shop\Repositories\SearchRepository;

class HomeController extends Controller
{
    /**
     * SearchRepository object
     *
     * @var SearchRepository
    */
    protected $searchRepository;

    /**
     * Create a new controller instance.
     *
     * @param SearchRepository $searchRepository
     * @return void
    */
    public function __construct(SearchRepository $searchRepository)
    {
        $this->searchRepository = $searchRepository;

        parent::__construct();
    }

    /**
     * loads the home page for the storefront
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
     */
    public function index()
    {
        return view('shop::home.index');
    }

    /**
     * loads the home page for the storefront
     *
     * @return void
     */
    public function notFound()
    {
        abort(404);
    }

    /**
     * Upload image for product search with machine learning
     *
     * @return string
     */
    public function upload()
    {
        return $this->searchRepository->uploadSearchImage(request()->all());
    }
}
