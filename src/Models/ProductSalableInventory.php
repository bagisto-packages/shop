<?php

namespace BagistoPackages\Shop\Models;

use Illuminate\Database\Eloquent\Model;
use BagistoPackages\Shop\Contracts\ProductSalableInventory as ProductSalableInventoryContract;

class ProductSalableInventory extends Model implements ProductSalableInventoryContract
{
    public $timestamps = false;

    protected $fillable = [
        'qty',
        'sold_qty',
        'product_id',
        'channel_id',
    ];

    /**
     * Get the channel owns the inventory.
     */
    public function channel()
    {
        return $this->belongsTo(ChannelProxy::modelClass());
    }

    /**
     * Get the product that owns the product inventory.
     */
    public function product()
    {
        return $this->belongsTo(ProductProxy::modelClass());
    }
}
