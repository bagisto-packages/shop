<?php

namespace BagistoPackages\Shop\Http\Controllers;

use BagistoPackages\Shop\Repositories\SearchRepository;

 class SearchController extends Controller
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
     * Index to handle the view loaded with the search results
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
     */
    public function index()
    {
        $results = $this->searchRepository->search(request()->all());

        return view('shop::search.search')->with('results', $results->count() ? $results : null);
    }
}
