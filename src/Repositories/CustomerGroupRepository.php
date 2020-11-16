<?php

namespace BagistoPackages\Shop\Repositories;

use BagistoPackages\Shop\Eloquent\Repository;

class CustomerGroupRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */

    function model()
    {
        return 'BagistoPackages\Shop\Contracts\CustomerGroup';
    }

    /**
     * @param array $data
     * @return \BagistoPackages\Shop\Contracts\CustomerGroup
     */
    public function create(array $data)
    {
        $customer = $this->model->create($data);

        return $customer;
    }

    /**
     * @param array $data
     * @param int $id
     * @param string $id
     * @return \BagistoPackages\Shop\Contracts\CustomerGroup
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function update(array $data, $id, $attribute = "id")
    {
        $customer = $this->find($id);

        $customer->update($data);

        return $customer;
    }
}
