<?php

namespace BagistoPackages\Shop\Models;

use Illuminate\Database\Eloquent\Model;
use BagistoPackages\Shop\Contracts\CountryStateTranslation as CountryStateTranslationContract;

class CountryStateTranslation extends Model implements CountryStateTranslationContract
{
    public $timestamps = false;

    protected $fillable = ['default_name'];
}
