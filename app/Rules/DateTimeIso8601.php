<?php
// Original by laravel-json-api:
// https://github.com/cloudcreativity/laravel-json-api/blob/8a118fc2b92eab9463f257f17060f72f035d9d05/src/Rules/DateTimeIso8601.php
// Modified to use ValidationRule as the old Rule class is deprecated

/*
 * Copyright 2023 Cloud Creativity Limited
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace App\Rules;

use \Closure;

use DateTime;
use Illuminate\Contracts\Validation\ValidationRule;

class DateTimeIso8601 implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $isValid = true;
        if (!is_string($value) || empty($value)) {
            $isValid = false;
        }
        if ($isValid) {
            $isValid = collect([
                'Y-m-d\TH:iP',
                'Y-m-d\TH:i:sP',
                'Y-m-d\TH:i:s.uP',
            ])->contains(function ($format) use ($value) {
                return $this->accept($value, $format);
            });
        }
        if (!$isValid) $fail('Include both date and time in ISO8601 format');
    }

    /**
     * @param string $value
     * @param string $format
     * @return bool
     */
    private function accept(string $value, string $format): bool
    {
        $date = DateTime::createFromFormat($format, $value);

        return $date instanceof DateTime;
    }

}
