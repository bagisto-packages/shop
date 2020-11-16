<?php

namespace BagistoPackages\Shop\CacheFilters;

use Intervention\Image\Image;
use Intervention\Image\Filters\FilterInterface;

class Large implements FilterInterface
{
    /**
     * @param Image $image
     * @return Image
     */
    public function applyFilter(Image $image)
    {
        return $image->resize(480, null, function ($constraint) {
            $constraint->aspectRatio();
        });
    }
}
