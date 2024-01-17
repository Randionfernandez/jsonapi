<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Slug implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (preg_match('/_/', $value)){
            $fail(trans('validation_custom.no_underscores'));
        }

        if (preg_match('/^-/', $value)){
            $fail(trans('validation_custom.no_starting_dashes'));
        }

        if (preg_match('/-$/', $value)){
            $fail(trans('validation_custom.no_ending_dashes'));
        }

        if (!preg_match('/[a-zA-Z0-9-]+/', $value)){
            $fail(trans('validation_custom.no_valid_characters'));
        }


    }
}
