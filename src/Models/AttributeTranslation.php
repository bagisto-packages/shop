<?php

namespace BagistoPackages\Shop\Models;

use Illuminate\Database\Eloquent\Model;
use BagistoPackages\Shop\Contracts\AttributeTranslation as AttributeTranslationContract;

class AttributeTranslation extends Model implements AttributeTranslationContract
{
    public $timestamps = false;

    protected $fillable = ['name'];
}
