<?php

namespace BagistoPackages\Shop\Models;

use Illuminate\Database\Eloquent\Model;
use BagistoPackages\Shop\Contracts\ProductReview as ProductReviewContract;

class ProductReview extends Model implements ProductReviewContract
{
    protected $fillable = [
        'comment',
        'title',
        'rating',
        'status',
        'product_id',
        'customer_id',
        'name',
    ];

    /**
     * Get the product attribute family that owns the product.
     */
    public function customer()
    {
        return $this->belongsTo(CustomerProxy::modelClass());
    }

    /**
     * Get the product.
     */
    public function product()
    {
        return $this->belongsTo(ProductProxy::modelClass());
    }
}
