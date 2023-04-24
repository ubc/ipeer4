<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * Stock Laravel boolean validation doesn't interpret the string 'true' as true
 * and 'false' as false. This custom rule lets us accept 'true' and 'false'
 * strings.
 */
class BoolStr implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return is_bool(toBoolean($value));
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.boolean');
    }
}

