<?php

namespace BagistoPackages\Shop\Repositories;

use BagistoPackages\Shop\Eloquent\Repository;

class AttributeOptionRepository extends Repository
{

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'BagistoPackages\Shop\Contracts\AttributeOption';
    }

    /**
     * @param array $data
     * @return  \BagistoPackages\Shop\Contracts\AttributeOption
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function create(array $data)
    {
        $option = parent::create($data);

        $this->uploadSwatchImage($data, $option->id);

        return $option;
    }

    /**
     * @param array $data
     * @param int $id
     * @param string $attribute
     * @return  \BagistoPackages\Shop\Contracts\AttributeOption
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(array $data, $id, $attribute = "id")
    {
        $option = parent::update($data, $id);

        $this->uploadSwatchImage($data, $id);

        return $option;
    }

    /**
     * @param array $data
     * @param int $optionId
     * @return void
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function uploadSwatchImage($data, $optionId)
    {
        if (!isset($data['swatch_value']) || !$data['swatch_value']) {
            return;
        }

        if ($data['swatch_value'] instanceof \Illuminate\Http\UploadedFile) {
            parent::update([
                'swatch_value' => $data['swatch_value']->store('attribute_option'),
            ], $optionId);
        }
    }
}
