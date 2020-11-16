<?php

namespace BagistoPackages\Shop\Repositories;

use BagistoPackages\Shop\Eloquent\Repository;
use Prettus\Repository\Traits\CacheableRepository;

class CountryRepository extends Repository
{
    use CacheableRepository;

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'BagistoPackages\Shop\Contracts\Country';
    }
}
