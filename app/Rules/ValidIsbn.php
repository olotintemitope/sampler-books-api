<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidIsbn implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $maxBookNumberLength = 10;

        $splitBookNumber = str_split($value);
        $bookNumberLength = count($splitBookNumber);

        if ($bookNumberLength < $maxBookNumberLength ||
            $bookNumberLength > $maxBookNumberLength) {
            return false;
        }

        $total = 0;
        $count = $bookNumberLength;

        foreach($splitBookNumber as $bookNumber) {
            $total += ($bookNumber * $count);
            --$count;
        }

        return ($total % 11) === 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */

    public function message(): string
    {
        return 'TThe :attribute must be a valid ISBN.';
    }
}
