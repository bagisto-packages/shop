<?php

namespace BagistoPackages\Shop\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use BagistoPackages\Shop\Contracts\Address as AddressContract;

abstract class Address extends Model implements AddressContract
{
    protected $table = 'addresses';

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'address_type',
        'customer_id',
        'cart_id',
        'order_id',
        'first_name',
        'last_name',
        'gender',
        'company_name',
        'address1',
        'address2',
        'postcode',
        'city',
        'state',
        'country',
        'email',
        'phone',
        'default_address',
        'vat_id',
        'additional',
    ];

    protected $casts = [
        'additional' => 'array',
    ];

    /**
     * Get all of the attributes for the attribute groups.
     */
    public function getNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get the customer record associated with the address.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
