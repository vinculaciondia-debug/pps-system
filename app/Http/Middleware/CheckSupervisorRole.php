<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Supervisor;
use Symfony\Component\HttpFoundation\Response;

class CheckSupervisorRole
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        // Permitir acceso si:
        // 1. Es supervisor
        // 2. Es admin con registro de supervisor
        // 3. Es vinculacion con registro de supervisor
        if ($user->rol === 'supervisor' || 
            (in_array($user->rol, ['admin', 'vinculacion']) && 
             Supervisor::where('user_id', $user->id)->exists())) {
            return $next($request);
        }
        
        abort(403, 'No tienes acceso a esta sección');
    }
}