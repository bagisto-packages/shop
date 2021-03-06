<?php

namespace BagistoPackages\Shop\Repositories;

use BagistoPackages\Shop\Eloquent\Repository;

class ProductFlatRepository extends Repository
{
    public function model()
    {
        return 'BagistoPackages\Shop\Contracts\ProductFlat';
    }

    /**
     * Maximum Price of Category Product
     *
     * @param null $category
     * @return float
     */
    public function getCategoryProductMaximumPrice($category = null)
    {
        if (!$category) {
            return $this->model->max('max_price');
        }

        return $this->model
            ->leftJoin('product_categories', 'product_flat.product_id', 'product_categories.product_id')
            ->where('product_categories.category_id', $category->id)
            ->max('max_price');
    }

    /**
     * get Category Product Attribute
     *
     * @param int $categoryId
     * @return array
     */
    public function getCategoryProductAttribute($categoryId)
    {
        $qb = $this->model
            ->leftJoin('product_categories', 'product_flat.product_id', 'product_categories.product_id')
            ->where('product_categories.category_id', $categoryId)
            ->where('product_flat.channel', core()->getCurrentChannelCode())
            ->where('product_flat.locale', app()->getLocale());

        $productArrributes = $qb->leftJoin('product_attribute_values as pa', 'product_flat.product_id', 'pa.product_id')
            ->pluck('pa.attribute_id')
            ->toArray();

        $productSuperArrributes = $qb->leftJoin('product_super_attributes as ps', 'product_flat.product_id', 'ps.product_id')
            ->pluck('ps.attribute_id')
            ->toArray();

        $productCategoryArrributes = array_unique(array_merge($productArrributes, $productSuperArrributes));

        return $productCategoryArrributes;
    }

    /**
     * get Filterable Attributes.
     *
     * @param array $category
     * @param array $products
     * @return \Illuminate\Support\Collection
     */
    public function getFilterableAttributes($category, $products)
    {
        $filterAttributes = [];

        if (count($category->filterableAttributes) > 0) {
            $filterAttributes = $category->filterableAttributes;
        } else {
            $categoryProductAttributes = $this->getCategoryProductAttribute($category->id);

            if ($categoryProductAttributes) {
                foreach (app('BagistoPackages\Shop\Repositories\AttributeRepository')->getFilterAttributes() as $filterAttribute) {
                    if (in_array($filterAttribute->id, $categoryProductAttributes)) {
                        $filterAttributes[] = $filterAttribute;
                    } else if ($filterAttribute ['code'] == 'price') {
                        $filterAttributes[] = $filterAttribute;
                    }
                }

                $filterAttributes = collect($filterAttributes);
            }
        }

        return $filterAttributes;
    }

    /**
     * filter attributes according to products
     *
     * @param array $category
     * @return \Illuminate\Support\Collection
     */
    public function getProductsRelatedFilterableAttributes($category)
    {
        $products = app('BagistoPackages\Shop\Repositories\ProductRepository')->getProductsRelatedToCategory($category->id);

        $filterAttributes = $this->getFilterableAttributes($category, $products);

        $allProductAttributeOptionsCode = [];

        foreach ($products as $key => $product) {
            foreach ($filterAttributes as $attribute) {
                if ($attribute->code <> 'price') {
                    if (isset($product[$attribute->code])) {
                        if (!in_array($product[$attribute->code], $allProductAttributeOptionsCode)) {
                            array_push($allProductAttributeOptionsCode, $product[$attribute->code]);
                        }
                    }
                }
            }
        }

        foreach ($filterAttributes as $attribute) {
            foreach ($attribute->options as $key => $option) {
                if (!in_array($option->id, $allProductAttributeOptionsCode)) {
                    unset($attribute->options[$key]);
                }
            }
        }

        return $filterAttributes;

    }

    /**
     * update product_flat custom column
     *
     * @param \BagistoPackages\Shop\Models\Attribute $attribute
     * @param \BagistoPackages\Shop\Listeners\ProductFlat $listener
     * @return
     */
    public function updateAttributeColumn(
        \BagistoPackages\Shop\Models\Attribute $attribute,
        \BagistoPackages\Shop\Listeners\ProductFlat $listener)
    {
        return $this->model
            ->leftJoin('product_attribute_values as v', function ($join) use ($attribute) {
                $join->on('product_flat.id', '=', 'v.product_id')
                    ->on('v.attribute_id', '=', \DB::raw($attribute->id));
            })->update(['product_flat.' . $attribute->code => \DB::raw($listener->attributeTypeFields[$attribute->type] . '_value')]);
    }

}
