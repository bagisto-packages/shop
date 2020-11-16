<?php

namespace BagistoPackages\Shop\Listeners;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use BagistoPackages\Shop\Repositories\AttributeRepository;
use BagistoPackages\Shop\Repositories\AttributeOptionRepository;
use BagistoPackages\Shop\Repositories\ProductFlatRepository;
use BagistoPackages\Shop\Repositories\ProductAttributeValueRepository;
use BagistoPackages\Shop\Helpers\ProductType;
use BagistoPackages\Shop\Models\ProductAttributeValue;

class ProductFlat
{
    /**
     * AttributeRepository Repository Object
     *
     * @var AttributeRepository
     */
    protected $attributeRepository;

    /**
     * AttributeOptionRepository Repository Object
     *
     * @var AttributeOptionRepository
     */
    protected $attributeOptionRepository;

    /**
     * ProductFlatRepository Repository Object
     *
     * @var ProductFlatRepository
     */
    protected $productFlatRepository;

    /**
     * ProductAttributeValueRepository Repository Object
     *
     * @var ProductAttributeValueRepository
     */
    protected $productAttributeValueRepository;

    /**
     * Attribute Object
     *
     * @var \BagistoPackages\Shop\Contracts\Attribute
     */
    protected $attribute;

    /**
     * @var array
     */
    public $attributeTypeFields = [
        'text' => 'text',
        'textarea' => 'text',
        'price' => 'float',
        'boolean' => 'boolean',
        'select' => 'integer',
        'multiselect' => 'text',
        'datetime' => 'datetime',
        'date' => 'date',
        'file' => 'text',
        'image' => 'text',
        'checkbox' => 'text',
    ];

    /**
     * Create a new listener instance.
     *
     * @param AttributeRepository $attributeRepository
     * @param AttributeOptionRepository $attributeOptionRepository
     * @param ProductFlatRepository $productFlatRepository
     * @param ProductAttributeValueRepository $productAttributeValueRepository
     * @return void
     */
    public function __construct(
        AttributeRepository $attributeRepository,
        AttributeOptionRepository $attributeOptionRepository,
        ProductFlatRepository $productFlatRepository,
        ProductAttributeValueRepository $productAttributeValueRepository
    )
    {
        $this->attributeRepository = $attributeRepository;
        $this->attributeOptionRepository = $attributeOptionRepository;
        $this->productAttributeValueRepository = $productAttributeValueRepository;
        $this->productFlatRepository = $productFlatRepository;
    }

    /**
     * After the attribute is created
     *
     * @param \BagistoPackages\Shop\Contracts\Attribute $attribute
     * @return false
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function afterAttributeCreatedUpdated($attribute)
    {
        if (!$attribute->is_user_defined) {
            return false;
        }

        if (!$attribute->use_in_flat) {
            $this->afterAttributeDeleted($attribute->id);
            return false;
        }

        if (!Schema::hasColumn('product_flat', $attribute->code)) {
            Schema::table('product_flat', function (Blueprint $table) use ($attribute) {
                $table->{$this->attributeTypeFields[$attribute->type]}($attribute->code)->nullable();

                if ($attribute->type == 'select' || $attribute->type == 'multiselect') {
                    $table->string($attribute->code . '_label')->nullable();
                }
            });
        }
    }

    /**
     * After the attribute is deleted
     *
     * @param int $attributeId
     * @return void
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function afterAttributeDeleted($attributeId)
    {
        $attribute = $this->attributeRepository->find($attributeId);

        if (Schema::hasColumn('product_flat', strtolower($attribute->code))) {
            Schema::table('product_flat', function (Blueprint $table) use ($attribute) {
                $table->dropColumn($attribute->code);

                if ($attribute->type == 'select' || $attribute->type == 'multiselect') {
                    $table->dropColumn($attribute->code . '_label');
                }
            });

            $this->productFlatRepository->updateAttributeColumn($attribute, $this);

        }
    }

    /**
     * Creates product flat
     *
     * @param \BagistoPackages\Shop\Contracts\Product $product
     * @return void
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function afterProductCreatedUpdated($product)
    {
        $this->createFlat($product);

        if (ProductType::hasVariants($product->type)) {
            foreach ($product->variants()->get() as $variant) {
                $this->createFlat($variant, $product);
            }
        }
    }

    /**
     * Creates product flat
     *
     * @param \BagistoPackages\Shop\Contracts\Product $product
     * @param null $parentProduct
     * @return void
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function createFlat($product, $parentProduct = null)
    {
        static $familyAttributes = [];

        static $superAttributes = [];

        if (!array_key_exists($product->attribute_family->id, $familyAttributes)) {
            $familyAttributes[$product->attribute_family->id] = $product->attribute_family->custom_attributes;
        }

        if ($parentProduct && !array_key_exists($parentProduct->id, $superAttributes)) {
            $superAttributes[$parentProduct->id] = $parentProduct->super_attributes()->pluck('code')->toArray();
        }

        if (isset($product['channels'])) {
            foreach ($product['channels'] as $channel) {
                $channel = app('BagistoPackages\Shop\Repositories\ChannelRepository')->findOrFail($channel);
                $channels[] = $channel['code'];
            }
        } elseif (isset($parentProduct['channels'])) {
            foreach ($parentProduct['channels'] as $channel) {
                $channel = app('BagistoPackages\Shop\Repositories\ChannelRepository')->findOrFail($channel);
                $channels[] = $channel['code'];
            }
        } else {
            $channels[] = core()->getDefaultChannelCode();
        }

        foreach (core()->getAllChannels() as $channel) {
            if (in_array($channel->code, $channels)) {
                foreach ($channel->locales as $locale) {
                    $productFlat = $this->productFlatRepository->findOneWhere([
                        'product_id' => $product->id,
                        'channel' => $channel->code,
                        'locale' => $locale->code,
                    ]);

                    if (!$productFlat) {
                        $productFlat = $this->productFlatRepository->create([
                            'product_id' => $product->id,
                            'channel' => $channel->code,
                            'locale' => $locale->code,
                        ]);
                    }

                    foreach ($familyAttributes[$product->attribute_family->id] as $attribute) {
                        if ($parentProduct && !in_array($attribute->code, array_merge($superAttributes[$parentProduct->id], ['sku', 'name', 'price', 'weight', 'status']))) {
                            continue;
                        }

                        if (in_array($attribute->code, ['tax_category_id'])) {
                            continue;
                        }

                        if (!Schema::hasColumn('product_flat', $attribute->code)) {
                            continue;
                        }

                        if ($attribute->value_per_channel) {
                            if ($attribute->value_per_locale) {
                                $productAttributeValue = $product->attribute_values()
                                    ->where('channel', $channel->code)
                                    ->where('locale', $locale->code)
                                    ->where('attribute_id', $attribute->id)
                                    ->first();
                            } else {
                                $productAttributeValue = $product->attribute_values()
                                    ->where('channel', $channel->code)
                                    ->where('attribute_id', $attribute->id)
                                    ->first();
                            }
                        } else {
                            if ($attribute->value_per_locale) {
                                $productAttributeValue = $product->attribute_values()->where('locale', $locale->code)->where('attribute_id', $attribute->id)->first();
                            } else {
                                $productAttributeValue = $product->attribute_values()->where('attribute_id', $attribute->id)->first();
                            }
                        }

                        $productFlat->{$attribute->code} = $productAttributeValue[ProductAttributeValue::$attributeTypeFields[$attribute->type]] ?? null;

                        if ($attribute->type == 'select') {
                            $attributeOption = $this->attributeOptionRepository->find($product->{$attribute->code});

                            if ($attributeOption) {
                                if ($attributeOptionTranslation = $attributeOption->translate($locale->code)) {
                                    $productFlat->{$attribute->code . '_label'} = $attributeOptionTranslation->label;
                                } else {
                                    $productFlat->{$attribute->code . '_label'} = $attributeOption->admin_name;
                                }
                            }
                        } elseif ($attribute->type == 'multiselect') {
                            $attributeOptionIds = explode(',', $product->{$attribute->code});

                            if (count($attributeOptionIds)) {
                                $attributeOptions = $this->attributeOptionRepository->findWhereIn('id', $attributeOptionIds);

                                $optionLabels = [];

                                foreach ($attributeOptions as $attributeOption) {
                                    if ($attributeOptionTranslation = $attributeOption->translate($locale->code)) {
                                        $optionLabels[] = $attributeOptionTranslation->label;
                                    } else {
                                        $optionLabels[] = $attributeOption->admin_name;
                                    }
                                }

                                $productFlat->{$attribute->code . '_label'} = implode(', ', $optionLabels);
                            }
                        }
                    }

                    $productFlat->created_at = $product->created_at;

                    $productFlat->updated_at = $product->updated_at;

                    $productFlat->min_price = $product->getTypeInstance()->getMinimalPrice();

                    $productFlat->max_price = $product->getTypeInstance()->getMaximamPrice();

                    if ($parentProduct) {
                        $parentProductFlat = $this->productFlatRepository->findOneWhere([
                            'product_id' => $parentProduct->id,
                            'channel' => $channel->code,
                            'locale' => $locale->code,
                        ]);

                        if ($parentProductFlat) {
                            $productFlat->parent_id = $parentProductFlat->id;
                        }
                    }

                    $productFlat->save();
                }
            } else {
                $route = request()->route() ? request()->route()->getName() : "";

                if ($route == 'admin.catalog.products.update') {
                    $productFlat = $this->productFlatRepository->findOneWhere([
                        'product_id' => $product->id,
                        'channel' => $channel->code,
                    ]);

                    if ($productFlat) {
                        $this->productFlatRepository->delete($productFlat->id);
                    }
                }
            }
        }
    }
}
