<?php

namespace BagistoPackages\Shop\Models;

use Illuminate\Database\Eloquent\Model;
use BagistoPackages\Shop\Contracts\CartPayment as CartPaymentContract;

class CartPayment extends Model implements CartPaymentContract
{
    protected $table = 'cart_payment';
}
