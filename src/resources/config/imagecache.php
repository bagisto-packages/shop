<?php

return [
    'route' => 'cache',
    'paths' => [
        storage_path('app/public'),
        public_path('storage')
    ],
    'templates' => [
        'small' => 'BagistoPackages\Shop\CacheFilters\Small',
        'medium' => 'BagistoPackages\Shop\CacheFilters\Medium',
        'large' => 'BagistoPackages\Shop\CacheFilters\Large',
    ],
    'lifetime' => 525600,
];
