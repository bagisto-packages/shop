<?php

namespace BagistoPackages\Shop\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use BagistoPackages\Shop\Repositories\ProductRepository;
use BagistoPackages\Shop\Repositories\ProductAttributeValueRepository;
use BagistoPackages\Shop\Models\ProductAttributeValue;

class ProductForm extends FormRequest
{
    /**
     * ProductRepository object
     *
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * ProductAttributeValueRepository object
     *
     * @var ProductAttributeValueRepository
     */
    protected $productAttributeValueRepository;

    /**
     * @var array
     */
    protected $rules;

    /**
     * Create a new form request instance.
     *
     * @param ProductRepository $productRepository
     * @param ProductAttributeValueRepository $productAttributeValueRepository
     * @return void
     */
    public function __construct(
        ProductRepository $productRepository,
        ProductAttributeValueRepository $productAttributeValueRepository
    )
    {
        $this->productRepository = $productRepository;
        $this->productAttributeValueRepository = $productAttributeValueRepository;
    }

    /**
     * Determine if the product is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function rules()
    {
        $product = $this->productRepository->find($this->id);

        $this->rules = array_merge($product->getTypeInstance()->getTypeValidationRules(), [
            'sku' => ['required', 'unique:products,sku,' . $this->id, new \BagistoPackages\Shop\Contracts\Validations\Slug],
            'images.*' => 'nullable|mimes:bmp,jpeg,jpg,png,webp',
            'special_price_from' => 'nullable|date',
            'special_price_to' => 'nullable|date|after_or_equal:special_price_from',
            'special_price' => ['nullable', new \BagistoPackages\Shop\Contracts\Validations\Decimal, 'lt:price'],
        ]);

        foreach ($product->getEditableAttributes() as $attribute) {
            if ($attribute->code == 'sku' || $attribute->type == 'boolean') {
                continue;
            }

            $validations = [];

            if (!isset($this->rules[$attribute->code])) {
                array_push($validations, $attribute->is_required ? 'required' : 'nullable');
            } else {
                $validations = $this->rules[$attribute->code];
            }

            if ($attribute->type == 'text' && $attribute->validation) {
                array_push($validations,
                    $attribute->validation == 'decimal'
                        ? new \BagistoPackages\Shop\Contracts\Validations\Decimal
                        : $attribute->validation
                );
            }

            if ($attribute->type == 'price') {
                array_push($validations, new \BagistoPackages\Shop\Contracts\Validations\Decimal);
            }

            if ($attribute->is_unique) {
                array_push($validations, function ($field, $value, $fail) use ($attribute) {
                    $column = ProductAttributeValue::$attributeTypeFields[$attribute->type];

                    if (!$this->productAttributeValueRepository->isValueUnique($this->id, $attribute->id, $column, request($attribute->code))) {
                        $fail('The :attribute has already been taken.');
                    }
                });
            }

            $this->rules[$attribute->code] = $validations;
        }

        return $this->rules;
    }

    /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages()
    {
        return [
            'variants.*.sku.unique' => 'The sku has already been taken.',
        ];
    }
}
