<?php

namespace BagistoPackages\Shop\Listeners;

use BagistoPackages\Shop\Helpers\CartRule;

class Cart
{
    /**
     * CartRule object
     *
     * @var CartRule
     */
    protected $cartRuleHelper;

    /**
     * Create a new listener instance.
     *
     * @param CartRule $cartRuleHelper
     */
    public function __construct(CartRule $cartRuleHelper)
    {
        $this->cartRuleHelper = $cartRuleHelper;
    }

    /**
     * Applly valid cart rules to cart
     *
     * @param \BagistoPackages\Shop\Contracts\Cart $cart
     * @return void
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function applyCartRules($cart)
    {
        $this->cartRuleHelper->collect();
    }
}
