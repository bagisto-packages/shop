<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use BagistoPackages\Shop\Models\Order;
use BagistoPackages\Shop\Models\Refund;

$factory->define(Refund::class, function (Faker $faker, array $attributes) {
    return [
        'order_id' => function () {
            return factory(Order::class)->create()->id;
        },
    ];
});

