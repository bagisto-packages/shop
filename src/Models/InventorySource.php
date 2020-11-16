<?php

namespace BagistoPackages\Shop\Models;

use Illuminate\Database\Eloquent\Model;
use BagistoPackages\Shop\Contracts\InventorySource as InventorySourceContract;

class InventorySource extends Model implements InventorySourceContract
{
    protected $guarded = ['_token'];
}
