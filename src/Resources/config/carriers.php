<?php

return [
    'flatrate' => [
        'code' => 'flatrate',
        'title' => 'Flat Rate',
        'description' => 'Flat Rate Shipping',
        'active' => true,
        'default_rate' => '10',
        'type' => 'per_unit',
        'class' => 'BagistoPackages\Shop\Carriers\FlatRate',
    ],

    'free' => [
        'code' => 'free',
        'title' => 'Free Shipping',
        'description' => 'Free Shipping',
        'active' => true,
        'default_rate' => '0',
        'class' => 'BagistoPackages\Shop\Carriers\Free',
    ]
];
