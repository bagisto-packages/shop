<?php

namespace BagistoPackages\Shop\Helpers;

use BagistoPackages\Shop\Models\Product;
use BagistoPackages\Shop\Models\ProductAttributeValue;

class ConfigurableOption extends AbstractProduct
{
    /**
     * ProductImage object
     *
     * @var array
     */
    protected $productImage;

    /**
     * Create a new controller instance.
     *
     * @param ProductImage $productImage
     * @return void
     */
    public function __construct(ProductImage $productImage)
    {
        $this->productImage = $productImage;
    }

    /**
     * Returns the allowed variants
     *
     * @param \BagistoPackages\Shop\Contracts\Product|\BagistoPackages\Shop\Contracts\ProductFlat $product
     * @return array
     */
    public function getAllowedProducts($product)
    {
        static $variants = [];

        if (count($variants)) {
            return $variants;
        }

        foreach ($product->variants as $variant) {
            if ($variant->isSaleable()) {
                $variants[] = $variant;
            }
        }

        return $variants;
    }

    /**
     * Returns the allowed variants JSON
     *
     * @param \BagistoPackages\Shop\Contracts\Product|\BagistoPackages\Shop\Contracts\ProductFlat $product
     * @return array
     */
    public function getConfigurationConfig($product)
    {
        $options = $this->getOptions($product, $this->getAllowedProducts($product));

        $config = [
            'attributes' => $this->getAttributesData($product, $options),
            'index' => isset($options['index']) ? $options['index'] : [],
            'regular_price' => [
                'formated_price' => core()->currency($product->getTypeInstance()->getMinimalPrice()),
                'price' => $product->getTypeInstance()->getMinimalPrice(),
            ],
            'variant_prices' => $this->getVariantPrices($product),
            'variant_images' => $this->getVariantImages($product),
            'chooseText' => trans('shop::app.products.choose-option'),
        ];

        return $config;
    }

    /**
     * Get allowed attributes
     *
     * @param \BagistoPackages\Shop\Contracts\Product|\BagistoPackages\Shop\Contracts\ProductFlat $product
     * @return \Illuminate\Support\Collection
     */
    public function getAllowAttributes($product)
    {
        return $product->product->super_attributes;
    }

    /**
     * Get Configurable Product Options
     *
     * @param \BagistoPackages\Shop\Contracts\Product|\BagistoPackages\Shop\Contracts\ProductFlat $currentProduct
     * @param array $allowedProducts
     * @return array
     */
    public function getOptions($currentProduct, $allowedProducts)
    {
        $options = [];

        $allowAttributes = $this->getAllowAttributes($currentProduct);

        foreach ($allowedProducts as $product) {
            if ($product instanceof \BagistoPackages\Shop\Models\ProductFlat) {
                $productId = $product->product_id;
            } else {
                $productId = $product->id;
            }

            foreach ($allowAttributes as $productAttribute) {
                $productAttributeId = $productAttribute->id;

                $attributeValue = $product->{$productAttribute->code};

                if ($attributeValue == '' && $product instanceof \BagistoPackages\Shop\Models\ProductFlat) {
                    $attributeValue = $product->product->{$productAttribute->code};
                }

                $options[$productAttributeId][$attributeValue][] = $productId;

                $options['index'][$productId][$productAttributeId] = $attributeValue;
            }
        }

        return $options;
    }

    /**
     * Get product attributes
     *
     * @param \BagistoPackages\Shop\Contracts\Product|\BagistoPackages\Shop\Contracts\ProductFlat $product
     * @param array $options
     * @return array
     */
    public function getAttributesData($product, array $options = [])
    {
        $defaultValues = [];

        $attributes = [];

        $allowAttributes = $this->getAllowAttributes($product);

        foreach ($allowAttributes as $attribute) {

            $attributeOptionsData = $this->getAttributeOptionsData($attribute, $options);

            if ($attributeOptionsData) {
                $attributeId = $attribute->id;

                $attributes[] = [
                    'id' => $attributeId,
                    'code' => $attribute->code,
                    'label' => $attribute->name ? $attribute->name : $attribute->admin_name,
                    'swatch_type' => $attribute->swatch_type,
                    'options' => $attributeOptionsData,
                ];
            }
        }

        return $attributes;
    }

    /**
     * @param \BagistoPackages\Shop\Contracts\Attribute $attribute
     * @param array $options
     * @return array
     */
    protected function getAttributeOptionsData($attribute, $options)
    {
        $attributeOptionsData = [];

        foreach ($attribute->options as $attributeOption) {

            $optionId = $attributeOption->id;

            if (isset($options[$attribute->id][$optionId])) {
                $attributeOptionsData[] = [
                    'id' => $optionId,
                    'label' => $attributeOption->label ? $attributeOption->label : $attributeOption->admin_name,
                    'swatch_value' => $attribute->swatch_type == 'image' ? $attributeOption->swatch_value_url : $attributeOption->swatch_value,
                    'products' => $options[$attribute->id][$optionId],
                ];
            }
        }

        return $attributeOptionsData;
    }

    /**
     * Get product prices for configurable variations
     *
     * @param \BagistoPackages\Shop\Contracts\Product|\BagistoPackages\Shop\Contracts\ProductFlat $product
     * @return array
     */
    protected function getVariantPrices($product)
    {
        $prices = [];

        foreach ($this->getAllowedProducts($product) as $variant) {
            if ($variant instanceof \BagistoPackages\Shop\Models\ProductFlat) {
                $variantId = $variant->product_id;
            } else {
                $variantId = $variant->id;
            }

            $prices[$variantId] = $variant->getTypeInstance()->getProductPrices();
        }

        return $prices;
    }

    /**
     * Get product images for configurable variations
     *
     * @param \BagistoPackages\Shop\Contracts\Product|\BagistoPackages\Shop\Contracts\ProductFlat $product
     * @return array
     */
    protected function getVariantImages($product)
    {
        $images = [];

        foreach ($this->getAllowedProducts($product) as $variant) {
            if ($variant instanceof \BagistoPackages\Shop\Models\ProductFlat) {
                $variantId = $variant->product_id;
            } else {
                $variantId = $variant->id;
            }

            $images[$variantId] = $this->productImage->getGalleryImages($variant);
        }

        return $images;
    }
}
