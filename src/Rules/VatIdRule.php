<?php

namespace BagistoPackages\Shop\Rules;

use Illuminate\Contracts\Validation\Rule;

class VatIdRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * The rules are borrowed from:
     * @see https://raw.githubusercontent.com/danielebarbaro/laravel-vat-eu-validator/master/src/VatValidator.php
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $validator = new VatValidator();

        return $validator->validate($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('shop::app.invalid_vat_format');
    }
}
