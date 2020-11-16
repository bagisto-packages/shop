<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use BagistoPackages\Shop\Models\TaxCategory;

$factory->define(TaxCategory::class, function (Faker $faker) {
    return [
        'code'        => $faker->uuid,
        'name'        => $faker->words(2, true),
        'description' => $faker->sentence(10),
    ];
});
