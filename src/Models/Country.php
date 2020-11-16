<?php

namespace BagistoPackages\Shop\Models;

use BagistoPackages\Shop\Eloquent\TranslatableModel;
use BagistoPackages\Shop\Contracts\Country as CountryContract;

class Country extends TranslatableModel implements CountryContract
{
    public $timestamps = false;

    public $translatedAttributes = ['name'];

    protected $with = ['translations'];
}
