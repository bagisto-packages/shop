<?php

namespace BagistoPackages\Shop\Http\Controllers;

use BagistoPackages\Shop\Repositories\SliderRepository;
use BagistoPackages\Shop\Repositories\SearchRepository;

class HomeController extends Controller
{
    /**
     * SliderRepository object
     *
     * @var SliderRepository
    */
    protected $sliderRepository;

    /**
     * SearchRepository object
     *
     * @var SearchRepository
    */
    protected $searchRepository;

    /**
     * Create a new controller instance.
     *
     * @param SliderRepository $sliderRepository
     * @param SearchRepository $searchRepository
     * @return void
    */
    public function __construct(SliderRepository $sliderRepository, SearchRepository $searchRepository)
    {
        $this->sliderRepository = $sliderRepository;
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
        $currentChannel = core()->getCurrentChannel();

        $currentLocale = core()->getCurrentLocale();

        $sliderData = $this->sliderRepository
            ->where('channel_id', $currentChannel->id)
            ->where('locale', $currentLocale->code)
            ->get()
            ->toArray();

        return view('shop::home.index', compact('sliderData'));
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
