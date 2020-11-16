<?php

namespace BagistoPackages\Shop\Models;

use Illuminate\Database\Eloquent\Model;
use BagistoPackages\Shop\Contracts\ProductBundleOptionTranslation as ProductBundleOptionTranslationContract;

class ProductBundleOptionTranslation extends Model implements ProductBundleOptionTranslationContract
{
    public $timestamps = false;

    protected $fillable = ['label'];
}
