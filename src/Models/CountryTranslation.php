<?php

namespace BagistoPackages\Shop\Models;

use Illuminate\Database\Eloquent\Model;
use BagistoPackages\Shop\Contracts\CountryTranslation as CountryTranslationContract;

class CountryTranslation extends Model implements CountryTranslationContract
{
    public $timestamps = false;

    protected $fillable = ['name'];
}
