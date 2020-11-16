<?php

namespace BagistoPackages\Shop\Repositories;

use Illuminate\Container\Container as App;
use BagistoPackages\Shop\Eloquent\Repository;
use BagistoPackages\Shop\Models\ProductAttributeValueProxy;

class ProductAttributeValueRepository extends Repository
{
    /**
     * AttributeRepository object
     *
     * @var AttributeRepository
     */
    protected $attributeRepository;

    /**
     * Create a new repository instance.
     *
     * @param AttributeRepository $attributeRepository
     * @param App $app
     * @return void
     */
    public function __construct(
        AttributeRepository $attributeRepository,
        App $app
    )
    {
        $this->attributeRepository = $attributeRepository;

        parent::__construct($app);
    }

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return 'BagistoPackages\Shop\Contracts\ProductAttributeValue';
    }

    /**
     * @param array $data
     * @return \BagistoPackages\Shop\Contracts\ProductAttributeValue
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function create(array $data)
    {
        if (isset($data['attribute_id'])) {
            $attribute = $this->attributeRepository->find($data['attribute_id']);
        } else {
            $attribute = $this->attributeRepository->findOneByField('code', $data['attribute_code']);
        }

        if (!$attribute) {
            return;
        }

        $data[ProductAttributeValueProxy::modelClass()::$attributeTypeFields[$attribute->type]] = $data['value'];

        return $this->model->create($data);
    }

    /**
     * @param string $column
     * @param int $attributeId
     * @param int $productId
     * @param string $value
     * @return boolean
     */
    public function isValueUnique($productId, $attributeId, $column, $value)
    {
        $result = $this->resetScope()->model->where($column, $value)->where('attribute_id', '=', $attributeId)->where('product_id', '!=', $productId)->get();

        return $result->count() ? false : true;
    }
}
