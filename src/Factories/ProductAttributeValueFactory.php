<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use BagistoPackages\Shop\Models\Product;
use BagistoPackages\Shop\Models\ProductAttributeValue;

$factory->define(ProductAttributeValue::class, function (Faker $faker) {
    return [
        'product_id' => function () {
            return factory(Product::class)->create()->id;
        },
        'locale'     => 'en',
        'channel'    => 'default',
    ];
});
