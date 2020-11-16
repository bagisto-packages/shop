<?php

namespace BagistoPackages\Shop\Repositories;

use BagistoPackages\Shop\Eloquent\Repository;
use BagistoPackages\Shop\Contracts\OrderAddress;

class OrderAddressRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return string
     */

    function model()
    {
        return OrderAddress::class;
    }
}
