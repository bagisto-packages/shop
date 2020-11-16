<?php

namespace BagistoPackages\Shop\Repositories;

use Illuminate\Container\Container as App;
use Illuminate\Support\Facades\Storage;
use BagistoPackages\Shop\Eloquent\Repository;

class SearchRepository extends Repository
{
    /**
     * ProductRepository object
     *
     * @return Object
     */
    protected $productRepository;

    /**
     * Create a new repository instance.
     *
     * @param ProductRepository $productRepository
     * @param App $app
     *
     * @return void
     */
    public function __construct(
        ProductRepository $productRepository,
        App $app
    )
    {
        parent::__construct($app);

        $this->productRepository = $productRepository;
    }

    function model()
    {
        return 'BagistoPackages\Shop\Contracts\Product';
    }

    public function search($data)
    {
        return $this->productRepository->searchProductByAttribute($data['term'] ?? '');
    }

    /**
     * @param array $data
     * @return string
     */
    public function uploadSearchImage($data)
    {
        $path = request()->file('image')->store('product-search');

        return Storage::url($path);
    }
}
