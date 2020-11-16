<?php

use BagistoPackages\Shop\Core;
use BagistoPackages\Shop\Cart;
use BagistoPackages\Shop\Bouncer;
use BagistoPackages\Shop\Payment;
use BagistoPackages\Shop\Shipping;
use BagistoPackages\Ui\ViewRenderEventManager;

if (!function_exists('core')) {
    function core()
    {
        return app()->make(Core::class);
    }
}

if (!function_exists('cart')) {
    function cart()
    {
        return app()->make(Cart::class);
    }
}

if (!function_exists('bouncer')) {
    function bouncer()
    {
        return app()->make(Bouncer::class);
    }
}

if (!function_exists('payment')) {
    function payment()
    {
        return new Payment;
    }
}

if (!function_exists('shipping')) {
    function shipping()
    {
        return new Shipping;
    }
}

if (!function_exists('themes')) {
    function themes()
    {
        return app()->make('themes');
    }
}

if (!function_exists('bagisto_asset')) {
    function bagisto_asset($path, $secure = null)
    {
        return themes()->url($path, $secure);
    }
}

if (!function_exists('view_render_event')) {
    function view_render_event($eventName, $params = null)
    {
        app()->singleton(ViewRenderEventManager::class);

        $viewEventManager = app()->make(ViewRenderEventManager::class);

        $viewEventManager->handleRenderEvent($eventName, $params);

        return $viewEventManager->render();
    }
}

if (!function_exists('array_permutation')) {
    function array_permutation($input)
    {
        $results = [];

        foreach ($input as $key => $values) {
            if (empty($values)) {
                continue;
            }

            if (empty($results)) {
                foreach ($values as $value) {
                    $results[] = [$key => $value];
                }
            } else {
                $append = [];

                foreach ($results as &$result) {
                    $result[$key] = array_shift($values);

                    $copy = $result;

                    foreach ($values as $item) {
                        $copy[$key] = $item;
                        $append[] = $copy;
                    }

                    array_unshift($values, $result[$key]);
                }

                $results = array_merge($results, $append);
            }
        }

        return $results;
    }
}
