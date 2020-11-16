<?php

namespace BagistoPackages\Shop\Repositories;

use BagistoPackages\Shop\Eloquent\Repository;

class BookingProductAppointmentSlotRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'BagistoPackages\Shop\Contracts\BookingProductAppointmentSlot';
    }
}
