<?php

namespace App\Actions\Fortify;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class CustomLoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('supervisor')) {
            return redirect()->route('supervisores.dashboard');
        }

        if ($user->hasRole('estudiante')) {
            return redirect()->route('estudiantes.solicitud');
        }

        return redirect()->intended(config('fortify.home'));
    }
}
