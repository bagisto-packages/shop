<?php

namespace BagistoPackages\Shop\Models;

use Illuminate\Database\Eloquent\Model;
use BagistoPackages\Shop\Contracts\CartRuleTranslation as CartRuleTranslationContract;

class CartRuleTranslation extends Model implements CartRuleTranslationContract
{
    public $timestamps = false;

    protected $fillable = ['label'];
}
