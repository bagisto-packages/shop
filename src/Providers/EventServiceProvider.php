<?php

namespace BagistoPackages\Shop\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $subscribe = [
        'BagistoPackages\Shop\Listeners\CustomerEventsHandler',
    ];
}
