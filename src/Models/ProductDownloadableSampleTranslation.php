<?php

namespace BagistoPackages\Shop\Models;

use Illuminate\Database\Eloquent\Model;
use BagistoPackages\Shop\Contracts\ProductDownloadableSampleTranslation as ProductDownloadableSampleTranslationContract;

class ProductDownloadableSampleTranslation extends Model implements ProductDownloadableSampleTranslationContract
{
    public $timestamps = false;

    protected $fillable = ['title'];
}
