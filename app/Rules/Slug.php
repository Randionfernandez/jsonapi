<?php
namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Slug implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (str_contains($value, '_')) {
            $fail(trans('validation_custom.no_underscores'));
        }

        if (str_starts_with($value, '-')) {
            $fail(trans('validation_custom.no_starting_dashes'));
        }

        if (str_ends_with($value, '-')) {
            $fail(trans('validation_custom.no_ending_dashes'));
        }

        if (str_contains( $value, ' ')) {   // No está permitido tener espacios en blanco.
            $fail(trans('validation_custom.no_spaces'));
        }

        if (!preg_match('/[a-zA-Z0-9-]+/', $value)) {
            $fail(trans('validation_custom.no_valid_characters'));
        }

    }
}
