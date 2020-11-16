<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use BagistoPackages\Shop\Models\Product;
use BagistoPackages\Shop\Models\ProductDownloadableLink;
use BagistoPackages\Shop\Models\ProductDownloadableLinkTranslation;

$factory->define(ProductDownloadableLink::class, function (Faker $faker) {
    $now = date("Y-m-d H:i:s");
    $filename = 'ProductImageExampleForUpload.jpg';
    $filepath = '/tests/_data/';

    return [
        'url'        => '',
        'file'       => $filepath . $filename,
        'file_name'  => $filename,
        'type'       => 'file',
        'price'      => 0.0000,
        'downloads'  => $faker->randomNumber(1),
        'product_id' => function () {
            return factory(Product::class)->create()->id;
        },
        'created_at' => $now,
        'updated_at' => $now,
    ];
});

$factory->define(ProductDownloadableLinkTranslation::class, function (Faker $faker) {
    return [
        'locale' => 'en',
        'title'  => $faker->word,
    ];
});
