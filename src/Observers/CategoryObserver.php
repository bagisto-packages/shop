<?php

namespace BagistoPackages\Shop\Observers;

use Illuminate\Support\Facades\Storage;

class CategoryObserver
{
    /**
     * Handle the Category "deleted" event.
     *
     * @param \BagistoPackages\Shop\Contracts\Category $category
     * @return void
     */
    public function deleted($category)
    {
        Storage::deleteDirectory('category/' . $category->id);
    }

    /**
     * Handle the Category "saved" event.
     *
     * @param \BagistoPackages\Shop\Contracts\Category $category
     */
    public function saved($category)
    {
        foreach ($category->children as $child) {
            $child->touch();
        }
    }
}
