<?php

namespace BagistoPackages\Shop\Models;

use Illuminate\Database\Eloquent\Model;
use BagistoPackages\Shop\Contracts\OrderComment as OrderCommentContract;

class OrderComment extends Model implements OrderCommentContract
{
    protected $fillable = [
        'comment',
        'customer_notified',
        'order_id',
    ];

    /**
     * Get the order record associated with the order comment.
     */
    public function order()
    {
        return $this->belongsTo(OrderProxy::modelClass());
    }
}
