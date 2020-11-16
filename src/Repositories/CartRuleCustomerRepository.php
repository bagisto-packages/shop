<?php

namespace BagistoPackages\Shop\Repositories;

use BagistoPackages\Shop\Eloquent\Repository;

class CartRuleCustomerRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'BagistoPackages\Shop\Contracts\CartRuleCustomer';
    }
}
