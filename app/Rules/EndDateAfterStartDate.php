<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class EndDateAfterStartDate implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $startDate = request()->input('start');

        // Check if both start and end times are valid and end time is after the start time with at least one minute difference
        if ($startDate && $value && strtotime($value) <= strtotime($startDate . '+1 minute')) {
            $fail('The end time should not come before start time.');
        }
    }
}
