<?php

namespace BagistoPackages\Shop\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use BagistoPackages\Shop\Models\Locale;
use BagistoPackages\Shop\Helpers\Locales;

class TranslatableModel extends Model
{
    use Translatable;

    protected function getLocalesHelper(): Locales
    {
        return app(Locales::class);
    }

    /**
     * @param string $key
     * @return bool
     */
    protected function isKeyALocale($key)
    {
        $chunks = explode('-', $key);

        if (count($chunks) > 1) {
            if (Locale::query()->where('code', '=', end($chunks))->first()) {
                return true;
            }
        } elseif (Locale::query()->where('code', '=', $key)->first()) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function locale()
    {
        if ($this->isChannelBased()) {
            return core()->getDefaultChannelLocaleCode();
        } else {
            if ($this->defaultLocale) {
                return $this->defaultLocale;
            }

            return config('translatable.locale') ?: app()->make('translator')->getLocale();
        }
    }

    /**
     * @return bool
     */
    protected function isChannelBased()
    {
        return false;
    }
}
