<?php

namespace BagistoPackages\Shop\Models;

use Illuminate\Database\Eloquent\Model;
use BagistoPackages\Shop\Contracts\AttributeGroup as AttributeGroupContract;

class AttributeGroup extends Model implements AttributeGroupContract
{
    public $timestamps = false;

    protected $fillable = ['name', 'position', 'is_user_defined'];

    /**
     * Get the attributes that owns the attribute group.
     */
    public function custom_attributes()
    {
        return $this->belongsToMany(AttributeProxy::modelClass(), 'attribute_group_mappings')
            ->withPivot('position')
            ->orderBy('pivot_position', 'asc');
    }
}
