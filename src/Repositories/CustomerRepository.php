<?php

namespace BagistoPackages\Shop\Repositories;

use BagistoPackages\Shop\Eloquent\Repository;

class CustomerRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */

    function model()
    {
        return 'BagistoPackages\Shop\Contracts\Customer';
    }

    /**
     * Check if customer has order pending or processing.
     *
     * @param \BagistoPackages\Shop\Models\Customer
     * @return boolean
     */
    public function checkIfCustomerHasOrderPendingOrProcessing($customer)
    {
        return $customer->all_orders->pluck('status')->contains(function ($val) {
            return $val === 'pending' || $val === 'processing';
        });
    }

    /**
     * Check if bulk customers, if they have order pending or processing.
     *
     * @param array
     * @return boolean
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function checkBulkCustomerIfTheyHaveOrderPendingOrProcessing($customerIds)
    {
        foreach ($customerIds as $customerId) {
            $customer = $this->findorFail($customerId);

            if ($this->checkIfCustomerHasOrderPendingOrProcessing($customer)) {
                return true;
            }
        }

        return false;
    }
}
