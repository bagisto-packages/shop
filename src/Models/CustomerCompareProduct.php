<?php

namespace BagistoPackages\Shop\Models;

use Illuminate\Database\Eloquent\Model;
use BagistoPackages\Shop\Contracts\CustomerCompareProduct as CustomerCompareProductContract;

class CustomerCompareProduct extends Model implements CustomerCompareProductContract
{
    protected $guarded = [];
}
