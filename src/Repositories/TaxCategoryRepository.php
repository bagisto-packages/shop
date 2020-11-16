<?php

namespace BagistoPackages\Shop\Repositories;

use BagistoPackages\Shop\Eloquent\Repository;

class TaxCategoryRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return 'BagistoPackages\Shop\Contracts\TaxCategory';
    }

    /**
     * @param \BagistoPackages\Shop\Contracts\TaxCategory $taxCategory
     * @param array $data
     * @return bool
     */
    public function attachOrDetach($taxCategory, $data)
    {
        $taxRates = $taxCategory->tax_rates;

        $this->model->findOrFail($taxCategory->id)->tax_rates()->sync($data);

        return true;
    }
}
