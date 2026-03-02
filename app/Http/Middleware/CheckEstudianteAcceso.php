<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Parametro;
use App\Models\SolicitudPPS;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;

class CheckEstudianteAcceso
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Solo aplicar a usuarios autenticados
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // Solo aplicar a estudiantes (rol = 'estudiante')
        if ($user->rol !== 'estudiante') {
            return $next($request);
        }

        // Obtener los días permitidos desde parámetros
        $diasPermitidos = Parametro::obtener('dias_acceso_post_finalizacion', 60);

        // Buscar la solicitud más reciente del estudiante con estado FINALIZADA
        $solicitudFinalizada = SolicitudPPS::where('user_id', $user->id)
            ->where('estado_solicitud', 'FINALIZADA')
            ->latest('updated_at')
            ->first();

        // Si no tiene solicitud finalizada, permitir acceso
        if (!$solicitudFinalizada) {
            return $next($request);
        }

        // Calcular cuántos días han pasado desde que finalizó
        $fechaFinalizacion = Carbon::parse($solicitudFinalizada->updated_at);
        $diasTranscurridos = $fechaFinalizacion->diffInDays(Carbon::now());

        // Si han pasado más días de los permitidos, bloquear acceso
        if ($diasTranscurridos > $diasPermitidos) {
            // Obtener el nombre del coordinador
            $coordinador = Parametro::obtener('coordinador_vinculacion', 'Coordinador de Vinculación');

            // Cerrar sesión
            Auth::logout();

            // Invalidar la sesión
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Redirigir al login con mensaje
            return redirect()->route('login')->with('status',
                "Tu práctica profesional finalizó hace más de {$diasPermitidos} días. " .
                "Tu acceso al sistema ha expirado. Para más información contacta a: {$coordinador}"
            );
        }

        // Si todo está bien, permitir acceso
        return $next($request);
    }
}