<?php

namespace BagistoPackages\Shop\Carriers;

use BagistoPackages\Shop\Models\CartShippingRate;

class Free extends AbstractShipping
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $code = 'free';

    /**
     * Returns rate for flatrate
     *
     * @return CartShippingRate|false
     */
    public function calculate()
    {
        if (!$this->isAvailable()) {
            return false;
        }

        $object = new CartShippingRate;

        $object->carrier = 'free';
        $object->carrier_title = $this->getConfigData('title');
        $object->method = 'free_free';
        $object->method_title = $this->getConfigData('title');
        $object->method_description = $this->getConfigData('description');
        $object->price = 0;
        $object->base_price = 0;

        return $object;
    }
}
