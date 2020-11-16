<?php

namespace BagistoPackages\Shop\Observers;

use Illuminate\Support\Facades\Storage;

class SliderObserver
{
    /**
     * Handle the Slider "deleted" event.
     *
     * @param \BagistoPackages\Shop\Contracts\Slider $slider
     * @return void
     */
    public function deleted($slider)
    {
        Storage::delete($slider->path);
    }
}
