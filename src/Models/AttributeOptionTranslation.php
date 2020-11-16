<?php

namespace BagistoPackages\Shop\Models;

use Illuminate\Database\Eloquent\Model;
use BagistoPackages\Shop\Contracts\AttributeOptionTranslation as AttributeOptionTranslationContract;

class AttributeOptionTranslation extends Model implements AttributeOptionTranslationContract
{
    public $timestamps = false;

    protected $fillable = ['label'];
}
