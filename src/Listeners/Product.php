<?php

namespace BagistoPackages\Shop\Listeners;

use BagistoPackages\Shop\Helpers\CatalogRuleIndex;

class Product
{
    /**
     * Product Repository Object
     *
     * @var CatalogRuleIndex
     */
    protected $catalogRuleIndexHelper;

    /**
     * Create a new listener instance.
     *
     * @param CatalogRuleIndex $catalogRuleIndexHelper
     * @return void
     */
    public function __construct(CatalogRuleIndex $catalogRuleIndexHelper)
    {
        $this->catalogRuleIndexHelper = $catalogRuleIndexHelper;
    }

    /**
     * @param \BagistoPackages\Shop\Contracts\Product $product
     * @return void
     */
    public function createProductRuleIndex($product)
    {
        $this->catalogRuleIndexHelper->reindexProduct($product);
    }
}
