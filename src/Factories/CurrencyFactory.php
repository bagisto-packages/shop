<?php

use Faker\Generator as Faker;
use BagistoPackages\Shop\Models\Currency;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Currency::class, function (Faker $faker, array $attributes) {

    return [
        'code' => $faker->unique()->currencyCode,
        'name' => $faker->word,
    ];

});
