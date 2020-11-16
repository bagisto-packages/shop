<?php

namespace BagistoPackages\Shop\Repositories;

use Illuminate\Container\Container as App;
use BagistoPackages\Shop\Eloquent\Repository;

class CatalogRuleRepository extends Repository
{
    /**
     * AttributeFamilyRepository object
     *
     * @var AttributeFamilyRepository
     */
    protected $attributeFamilyRepository;

    /**
     * AttributeRepository object
     *
     * @var AttributeRepository
     */
    protected $attributeRepository;

    /**
     * CategoryRepository class
     *
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * TaxCategoryRepository class
     *
     * @var TaxCategoryRepository
     */
    protected $taxCategoryRepository;

    /**
     * Create a new repository instance.
     *
     * @param AttributeFamilyRepository $attributeFamilyRepository
     * @param AttributeRepository $attributeRepository
     * @param CategoryRepository $categoryRepository
     * @param TaxCategoryRepository $taxCategoryRepository
     * @param App $app
     * @return void
     */
    public function __construct(
        AttributeFamilyRepository $attributeFamilyRepository,
        AttributeRepository $attributeRepository,
        CategoryRepository $categoryRepository,
        TaxCategoryRepository $taxCategoryRepository,
        App $app
    )
    {
        $this->attributeFamilyRepository = $attributeFamilyRepository;
        $this->attributeRepository = $attributeRepository;
        $this->categoryRepository = $categoryRepository;
        $this->taxCategoryRepository = $taxCategoryRepository;

        parent::__construct($app);
    }

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'BagistoPackages\Shop\Contracts\CatalogRule';
    }

    /**
     * @param array $data
     * @return \BagistoPackages\Shop\Contracts\CatalogRule
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function create(array $data)
    {
        $data['starts_from'] = $data['starts_from'] ?: null;

        $data['ends_till'] = $data['ends_till'] ?: null;

        $data['status'] = !isset($data['status']) ? 0 : 1;

        $catalogRule = parent::create($data);

        $catalogRule->channels()->sync($data['channels']);

        $catalogRule->customer_groups()->sync($data['customer_groups']);

        return $catalogRule;
    }

    /**
     * @param array $data
     * @param int $id
     * @param string $attribute
     * @return \BagistoPackages\Shop\Contracts\CatalogRule
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(array $data, $id, $attribute = "id")
    {
        $data['starts_from'] = $data['starts_from'] ?: null;

        $data['ends_till'] = $data['ends_till'] ?: null;

        $data['status'] = !isset($data['status']) ? 0 : 1;

        $data['conditions'] = $data['conditions'] ?? [];

        $catalogRule = $this->find($id);

        parent::update($data, $id, $attribute);

        $catalogRule->channels()->sync($data['channels']);

        $catalogRule->customer_groups()->sync($data['customer_groups']);

        return $catalogRule;
    }

    /**
     * Returns attributes for catalog rule conditions
     *
     * @return array
     */
    public function getConditionAttributes()
    {
        $attributes = [
            [
                'key' => 'product',
                'label' => trans('admin::app.promotions.catalog-rules.product-attribute'),
                'children' => [
                    [
                        'key' => 'product|category_ids',
                        'type' => 'multiselect',
                        'label' => trans('admin::app.promotions.catalog-rules.categories'),
                        'options' => $this->categoryRepository->getCategoryTree(),
                    ], [
                        'key' => 'product|attribute_family_id',
                        'type' => 'select',
                        'label' => trans('admin::app.promotions.catalog-rules.attribute_family'),
                        'options' => $this->getAttributeFamilies(),
                    ]
                ]
            ]
        ];

        foreach ($this->attributeRepository->findWhereNotIn('type', ['textarea', 'image', 'file']) as $attribute) {
            $attributeType = $attribute->type;

            if ($attribute->code == 'tax_category_id') {
                $options = $this->getTaxCategories();
            } else {
                if ($attribute->type === 'select') {
                    $options = $attribute->options()->orderBy('sort_order')->get();
                } else {
                    $options = $attribute->options;
                }
            }

            if ($attribute->validation == 'decimal')
                $attributeType = 'decimal';

            if ($attribute->validation == 'numeric')
                $attributeType = 'integer';

            $attributes[0]['children'][] = [
                'key' => 'product|' . $attribute->code,
                'type' => $attribute->type,
                'label' => $attribute->name,
                'options' => $options,
            ];
        }

        return $attributes;
    }

    /**
     * Returns all tax categories
     *
     * @return array
     */
    public function getTaxCategories()
    {
        $taxCategories = [];

        foreach ($this->taxCategoryRepository->all() as $taxCategory) {
            $taxCategories[] = [
                'id' => $taxCategory->id,
                'admin_name' => $taxCategory->name,
            ];
        }

        return $taxCategories;
    }

    /**
     * Returns all attribute families
     *
     * @return array
     */
    public function getAttributeFamilies()
    {
        $attributeFamilies = [];

        foreach ($this->attributeFamilyRepository->all() as $attributeFamily) {
            $attributeFamilies[] = [
                'id' => $attributeFamily->id,
                'admin_name' => $attributeFamily->name,
            ];
        }

        return $attributeFamilies;
    }
}
