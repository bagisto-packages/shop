<?php

namespace BagistoPackages\Shop\Models;

use Illuminate\Database\Eloquent\Model;
use BagistoPackages\Shop\Contracts\CustomerGroup as CustomerGroupContract;

class CustomerGroup extends Model implements CustomerGroupContract
{
    protected $table = 'customer_groups';

    protected $fillable = ['name', 'code', 'is_user_defined'];

    /**
     * Get the customer for this group.
     */
    public function customer()
    {
        return $this->hasMany(CustomerProxy::modelClass());
    }
}
