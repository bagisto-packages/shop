<?php

namespace BagistoPackages\Shop\Helpers;

class AppointmentSlot extends Booking
{
    /**
     * @param int $qty
     * @param \BagistoPackages\Shop\Contracts\BookingProduct $bookingProduct
     * @return bool
     */
    public function haveSufficientQuantity(int $qty, $bookingProduct): bool
    {
        return true;
    }
}
