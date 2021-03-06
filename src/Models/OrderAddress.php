<?php

namespace BagistoPackages\Shop\Models;

use BagistoPackages\Shop\Contracts\OrderAddress as OrderAddressContract;
use Illuminate\Database\Eloquent\Builder;

class OrderAddress extends Address implements OrderAddressContract
{
    public const ADDRESS_TYPE_SHIPPING = 'order_shipping';
    public const ADDRESS_TYPE_BILLING = 'order_billing';

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
        static::addGlobalScope('address_type', function (Builder $builder) {
            $builder->whereIn('address_type', [
                self::ADDRESS_TYPE_BILLING,
                self::ADDRESS_TYPE_SHIPPING
            ]);
        });

        static::creating(static function ($address) {
            switch ($address->address_type) {
                case CartAddress::ADDRESS_TYPE_BILLING:
                    $address->address_type = self::ADDRESS_TYPE_BILLING;
                    break;
                case CartAddress::ADDRESS_TYPE_SHIPPING:
                    $address->address_type = self::ADDRESS_TYPE_SHIPPING;
                    break;
            }
        });

        parent::boot();
    }

    /**
     * Get the order record associated with the address.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
