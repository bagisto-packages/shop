<?php

namespace BagistoPackages\Shop\Models;

use Illuminate\Database\Eloquent\Model;
use BagistoPackages\Shop\Contracts\ProductDownloadableLinkTranslation as ProductDownloadableLinkTranslationContract;

class ProductDownloadableLinkTranslation extends Model implements ProductDownloadableLinkTranslationContract
{
    public $timestamps = false;

    protected $fillable = ['title'];
}
