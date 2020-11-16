<?php

namespace BagistoPackages\Shop\Repositories;

use BagistoPackages\Shop\Eloquent\Repository;
use BagistoPackages\Shop\Contracts\OrderComment;

class OrderCommentRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return OrderComment::class;
    }
}
