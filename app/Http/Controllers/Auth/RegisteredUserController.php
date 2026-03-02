<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => app(PasswordValidationRules::class)->rules(),
        ]);
    
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
    
        // Asignar rol según correo
        if (str_ends_with($user->email, '@unah.hn')) {
            $user->assignRole('estudiante');
        } elseif (str_ends_with($user->email, '@unah.edu.hn')) {
            $user->assignRole('supervisor');
        }
    
        // Verificación de correo (si está habilitada)
        event(new Registered($user));
    
        // No loguear al usuario todavía (opcional)
        // Auth::login($user);
    
        return redirect()->route('login')->with('status', '¡Registro exitoso! Por favor revisa tu correo para verificar tu cuenta.');
    }
    
}
