<?php

namespace BagistoPackages\Shop\Models;

use Illuminate\Database\Eloquent\Model;
use BagistoPackages\Shop\Contracts\OrderPayment as OrderPaymentContract;


class OrderPayment extends Model implements OrderPaymentContract
{
    protected $table = 'order_payment';

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];
}
