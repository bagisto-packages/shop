<?php

namespace BagistoPackages\Shop\Repositories;

use BagistoPackages\Shop\Eloquent\Repository;

class TaxMapRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'BagistoPackages\Shop\Contracts\TaxMap';
    }

    /**
     * @param array $data
     * @return \BagistoPackages\Shop\Contracts\TaxMap
     */
    public function create(array $data)
    {
        $taxMap = $this->model->create($data);

        return $taxMap;
    }

    /**
     * @param array $data
     * @param int $id
     * @param string $attribute
     * @return \BagistoPackages\Shop\Contracts\TaxMap
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function update(array $data, $id, $attribute = "id")
    {
        $taxMap = $this->find($id);

        $taxMap->update($data);

        return $taxMap;
    }
}
