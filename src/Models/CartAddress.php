<?php

namespace BagistoPackages\Shop\Models;

use Illuminate\Database\Eloquent\Builder;
use BagistoPackages\Shop\Contracts\CartAddress as CartAddressContract;

class CartAddress extends Address implements CartAddressContract
{
    public const ADDRESS_TYPE_SHIPPING = 'cart_shipping';
    public const ADDRESS_TYPE_BILLING = 'cart_billing';

    /**
     * @var array default values
     */
    protected $attributes = [
        'address_type' => self::ADDRESS_TYPE_BILLING,
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        static::addGlobalScope('address_type', static function (Builder $builder) {
            $builder->whereIn('address_type', [
                self::ADDRESS_TYPE_BILLING,
                self::ADDRESS_TYPE_SHIPPING
            ]);
        });

        parent::boot();
    }

    /**
     * Get the shipping rates for the cart address.
     */
    public function shipping_rates()
    {
        return $this->hasMany(CartShippingRateProxy::modelClass());
    }

    /**
     * Get the cart record associated with the address.
     */
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }
}
