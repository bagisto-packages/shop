<?php

namespace BagistoPackages\Shop\Repositories;

use BagistoPackages\Shop\Eloquent\Repository;

class ProductReviewRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'BagistoPackages\Shop\Contracts\ProductReview';
    }

    /**
     * Retrieve review for customerId
     *
     * @return
     */
    function getCustomerReview()
    {
        $customerId = auth()->guard('customer')->user()->id;

        $reviews = $this->model->where(['customer_id' => $customerId])->with('product')->paginate(5);

        return $reviews;
    }
}
