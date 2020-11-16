<?php

namespace BagistoPackages\Shop\Repositories;

use Illuminate\Container\Container as App;
use BagistoPackages\Shop\Eloquent\Repository;
use Illuminate\Support\Str;

class ProductBundleOptionRepository extends Repository
{
    /**
     * ProductBundleOptionProductRepository object
     *
     * @var ProductBundleOptionProductRepository
     */
    protected $productBundleOptionProductRepository;

    /**
     * Create a new repository instance.
     *
     * @param ProductBundleOptionProductRepository $productBundleOptionProductRepository
     * @param App $app
     */
    public function __construct(
        ProductBundleOptionProductRepository $productBundleOptionProductRepository,
        App $app
    )
    {
        $this->productBundleOptionProductRepository = $productBundleOptionProductRepository;

        parent::__construct($app);
    }

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return 'BagistoPackages\Shop\Contracts\ProductBundleOption';
    }

    /**
     * @param array $data
     * @param \BagistoPackages\Shop\Contracts\Product $product
     * @return void
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function saveBundleOptons($data, $product)
    {
        $previousBundleOptionIds = $product->bundle_options()->pluck('id');

        if (isset($data['bundle_options'])) {
            foreach ($data['bundle_options'] as $bundleOptionId => $bundleOptionInputs) {
                if (Str::contains($bundleOptionId, 'option_')) {
                    $productBundleOption = $this->create(array_merge([
                        'product_id' => $product->id,
                    ], $bundleOptionInputs));
                } else {
                    $productBundleOption = $this->find($bundleOptionId);

                    if (is_numeric($index = $previousBundleOptionIds->search($bundleOptionId))) {
                        $previousBundleOptionIds->forget($index);
                    }

                    $this->update($bundleOptionInputs, $bundleOptionId);
                }

                $this->productBundleOptionProductRepository->saveBundleOptonProducts($bundleOptionInputs, $productBundleOption);
            }
        }

        foreach ($previousBundleOptionIds as $previousBundleOptionId) {
            $this->delete($previousBundleOptionId);
        }
    }
}
