<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;


class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Create a new user instance after a valid registration.
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255', 'regex:/^[A-ZÁÉÍÓÚÑ\s]+$/'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();
    
        // Validación personalizada de contraseña
        if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*#?&]).{8,}$/', $input['password'])) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'password' => 'La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial.',
            ]);
        }
    
        // Detectar rol basado en correo
        $rol = str_ends_with($input['email'], '@unah.hn') ? 'estudiante' :
               (str_ends_with($input['email'], '@unah.edu.hn') ? 'supervisor' : 'estudiante');
    
        // Obtener el ID del rol desde la tabla roles
        $codRol = \DB::table('roles')->where('name', $rol)->value('id');
    
        if (!$codRol) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'email' => 'No se encontró el rol "' . $rol . '" en la tabla roles.',
            ]);
        }
    
        return \DB::transaction(function () use ($input, $rol, $codRol) {
            return User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => \Hash::make($input['password']),
                'rol' => $rol,
                'cod_rol' => $codRol,
            ]);
        });
    }
    
}
