<?php

namespace BagistoPackages\Shop\Listeners;

class CustomerEventsHandler
{

    /**
     * Handle Customer login events.
     */
    public function onCustomerLogin()
    {
        /**
         * handle the user login event to manage the after login, if the user has added any products as guest then
         * the cart items from session will be transferred from cookie to the cart table in the database.
         *
         * Check whether cookie is present or not and then check emptiness and then do the appropriate actions.
         */
        \BagistoPackages\Shop\Facades\Cart::mergeCart();
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param \Illuminate\Events\Dispatcher $events
     * @return void
     */
    public function subscribe($events)
    {
        $events->listen('customer.after.login', 'BagistoPackages\Shop\Listeners\CustomerEventsHandler@onCustomerLogin');
    }
}
