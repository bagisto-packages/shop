<?php

namespace BagistoPackages\Shop\Http\Controllers;

use BagistoPackages\Shop\Repositories\CmsRepository;

class PagePresenterController extends Controller
{
    /**
     * CmsRepository object
     *
     * @var CmsRepository
     */
    protected $cmsRepository;

    /**
     * Create a new controller instance.
     *
     * @param CmsRepository $cmsRepository
     * @return void
     */
    public function __construct(CmsRepository $cmsRepository)
    {
        $this->cmsRepository = $cmsRepository;

        parent::__construct();
    }

    /**
     * To extract the page content and load it in the respective view file
     *
     * @param string $urlKey
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
     */
    public function presenter($urlKey)
    {
        $page = $this->cmsRepository->findByUrlKeyOrFail($urlKey);

        return view('shop::cms.page')->with('page', $page);
    }
}
