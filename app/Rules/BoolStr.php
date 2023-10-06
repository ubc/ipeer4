<?php

namespace App\Rules;

use Closure;

use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Stock Laravel boolean validation doesn't interpret the string 'true' as true
 * and 'false' as false. This custom rule lets us accept 'true' and 'false'
 * strings.
 */
class BoolStr implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!self::isBool($value)) {
            $fail('Enter true ("1", "true") or false ("0", "false")');
        }
    }

    public static function isBool(string $value): bool
    {
        return is_bool(toBoolean($value));
    }
}

