<?php

namespace BagistoPackages\Shop\Helpers;

class View extends AbstractProduct
{
    /**
     * Returns the visible custom attributes
     *
     * @param \BagistoPackages\Shop\Contracts\Product|\BagistoPackages\Shop\Contracts\ProductFlat $product
     * @return void|array
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function getAdditionalData($product)
    {
        $data = [];

        $attributes = $product->attribute_family->custom_attributes()->where('attributes.is_visible_on_front', 1)->get();

        $attributeOptionReposotory = app('BagistoPackages\Shop\Repositories\AttributeOptionRepository');

        foreach ($attributes as $attribute) {
            if ($product instanceof \BagistoPackages\Shop\Models\ProductFlat) {
                $value = $product->product->{$attribute->code};
            } else {
                $value = $product->{$attribute->code};
            }

            if ($attribute->type == 'boolean') {
                $value = $value ? 'Yes' : 'No';
            } elseif ($value) {
                if ($attribute->type == 'select') {
                    $attributeOption = $attributeOptionReposotory->find($value);

                    if ($attributeOption) {
                        $value = $attributeOption->label ?? null;

                        if (!$value) {
                            continue;
                        }
                    }
                } elseif ($attribute->type == 'multiselect' || $attribute->type == 'checkbox') {
                    $lables = [];

                    $attributeOptions = $attributeOptionReposotory->findWhereIn('id', explode(",", $value));

                    foreach ($attributeOptions as $attributeOption) {
                        if ($label = $attributeOption->label) {
                            $lables[] = $label;
                        }
                    }

                    $value = implode(", ", $lables);
                }
            }

            $data[] = [
                'id' => $attribute->id,
                'code' => $attribute->code,
                'label' => $attribute->name,
                'value' => $value,
                'admin_name' => $attribute->admin_name,
                'type' => $attribute->type,
            ];
        }

        return $data;
    }
}
