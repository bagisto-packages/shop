<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use BagistoPackages\Shop\Models\InventorySource;
use BagistoPackages\Shop\Models\Product;
use BagistoPackages\Shop\Models\ProductInventory;

$factory->define(ProductInventory::class, function (Faker $faker) {
    return [
        'qty'                 => $faker->numberBetween(100, 200),
        'product_id'          => function () {
            return factory(Product::class)->create()->id;
        },
        'inventory_source_id' => function () {
            return factory(InventorySource::class)->create()->id;
        },
    ];
});
