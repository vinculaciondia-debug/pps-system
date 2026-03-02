<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\ResetsUserPasswords;

class ResetUserPassword implements ResetsUserPasswords
{
    use PasswordValidationRules;

    /**
     * Reset the given user's password.
     */
    public function reset(User $user, array $input): void
    {
        Validator::make($input, [
            'email' => ['required', 'email'],
            'password' => $this->passwordRules(),
        ])->validate();

        // Validación personalizada de contraseña fuerte
        if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*#?&]).{8,}$/', $input['password'])) {
            throw ValidationException::withMessages([
                'password' => 'La nueva contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial.',
            ]);
        }

        // Guardar nueva contraseña
        $user->forceFill([
            'password' => Hash::make($input['password']),
        ])->save();

        // Opcional: cerrar otras sesiones activas
        $user->setRememberToken(str()->random(60));

        Password::deleteToken($user);
    }
}
