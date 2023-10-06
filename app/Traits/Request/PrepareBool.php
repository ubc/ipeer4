<?php

namespace App\Traits\Request;

use App\Models\Course;
use App\Models\Permission;
use App\Models\User;

use App\Rules\BoolStr;

/**
 * Meant to be used in Request classes for preparing boolean values for
 * validation.
 */
trait PrepareBool
{
    /**
     * Check to see if the input $attribute can be treated as a boolean, if so,
     * convert it to an actual boolean.
     *
     * The goal is to make sure we don't mistakenly convert invalid values to
     * boolean, we want invalid values to be passed to the validator untouched.
     */
    public function prepareBool(string $attribute): void
    {
        $value = $this->input($attribute);
        if (isset($value) && BoolStr::isBool($value)) {
            $this->merge([$attribute => toBoolean($value)]);
        }
    }

}
