<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use BagistoPackages\Shop\Models\CartPayment;

$factory->define(CartPayment::class, function (Faker $faker) {
    $now = date("Y-m-d H:i:s");

    return [
        'created_at' => $now,
        'updated_at' => $now,
    ];
});

