<?php


namespace App\Http\Traits;


trait ValidationTrait
{
    /**
     * Return password validation rules
     * @return string[]
     */
    public function getPasswordValidation(): array
    {
        return [
            'required',
            'min:8',
            'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/'
        ];
    }

    /**
     * Return password validation rules
     * @return string[]
     */
    public function getOptionalPasswordValidation(): array
    {
        return [
            'sometimes',
            'min:8',
            'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/'
        ];
    }
}