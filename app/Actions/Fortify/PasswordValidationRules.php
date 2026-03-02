<?php

namespace App\Actions\Fortify;

use Illuminate\Validation\Rules\Password;

trait PasswordValidationRules
{
    /**
     * Get the validation rules used to validate passwords.
     *
     * @return array<int, \Illuminate\Contracts\Validation\Rule|array<mixed>|string>
     */
    protected function passwordRules(): array
    {
        return [ 
        'required',
        'string',
        'min:8',
        'confirmed',
        'regex:/[A-Z]/',      // al menos una mayúscula
        'regex:/[a-z]/',      // al menos una minúscula
        'regex:/[0-9]/',      // al menos un número
        'regex:/[@$!%*#?&]/', // al menos un símbolo
        ];
    }
}
