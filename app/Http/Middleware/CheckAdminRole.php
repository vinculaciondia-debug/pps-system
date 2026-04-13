<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminRole
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && in_array($user->rol, ['admin', 'vinculacion'])) {
            return $next($request);
        }

        abort(403, 'No tienes acceso a esta sección');
    }
}
