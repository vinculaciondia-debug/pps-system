<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Models\Supervisor;
use App\Models\User;
use App\Models\SolicitudPPS;

class SupervisorController extends Controller
{
    /**
     * Mostrar todos los supervisores
     */
public function index(Request $request)
{
    $busqueda = $request->get('busqueda');
    $estado   = $request->get('estado');
    $perPage  = (int) $request->query('per_page', config('pagination.per_page', 10));

    $query = Supervisor::with('user');

    if ($busqueda) {
        $query->whereHas('user', function ($q) use ($busqueda) {
            $q->where('name', 'like', "%{$busqueda}%")
              ->orWhere('email', 'like', "%{$busqueda}%");
        });
    }
    if ($estado === 'activo') $query->where('activo', 1);
    if ($estado === 'inactivo') $query->where('activo', 0);

    $supervisores = $query->orderByDesc('activo')->latest('id')
        ->paginate($perPage)->withQueryString();

    $contadores = [
        'total'     => Supervisor::count(),
        'activos'   => Supervisor::where('activo', 1)->count(),
        'inactivos' => Supervisor::where('activo', 0)->count(),
        'llenos'    => 0, // opcional, calcula si lo necesitas
    ];

    return view('admin.supervisores', compact('supervisores','contadores'));
}

    /**
     * Obtener datos de un supervisor específico
     */
    public function show($id)
    {
        try {
            $supervisor = Supervisor::with([
                'user',
                'solicitudes' => function($query) {
                    $query->with('user')
                        ->whereIn('estado_solicitud', ['SOLICITADA', 'APROBADA']);
                }
            ])->findOrFail($id);
            
           
            $estudiantesAsignados = $supervisor->estudiantes_asignados;
            
            
            $cuposDisponibles = $supervisor->cupos_disponibles;
            
            return response()->json([
                'success' => true,
                'supervisor' => [
                    'id' => $supervisor->id,
                    'user' => $supervisor->user,
                    'max_estudiantes' => $supervisor->max_estudiantes,
                    'activo' => $supervisor->activo,
                    'solicitudes_asignadas' => $supervisor->solicitudes, // ← Solo activas
                ],
                'estudiantes_asignados' => $estudiantesAsignados,
                'cupos_disponibles' => $cuposDisponibles,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al cargar supervisor: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar el supervisor'
            ], 500);
        }
    }

    /**
     * Crear un nuevo supervisor
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'max_estudiantes' => 'required|integer|min:1|max:100',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'email.required' => 'El correo es obligatorio',
            'email.unique' => 'Este correo ya está registrado',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
            'max_estudiantes.required' => 'La capacidad máxima es obligatoria',
            'max_estudiantes.min' => 'La capacidad debe ser al menos 1',
            'max_estudiantes.max' => 'La capacidad no puede superar 100',
        ]);

        try {
            \DB::beginTransaction();

            // Crear usuario
            $user = User::create([
                'name' => $request->nombre,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'rol' => 'supervisor',
                'cod_rol' => 3,  // Código de rol 
                'email_verified_at' => now(),
            ]);

            // Crear supervisor
            $supervisor = Supervisor::create([
                'user_id' => $user->id,
                'max_estudiantes' => $request->max_estudiantes,
                'activo' => 1,
            ]);

            \DB::commit();

            Log::info('Supervisor creado: ' . $user->name . ' (ID: ' . $supervisor->id . ')');

            return redirect()
                ->route('admin.supervisores.index')
                ->with('success', 'Supervisor ' . $user->name . ' creado exitosamente');

        } catch (\Exception $e) {
            \DB::rollBack();
            
            Log::error('Error al crear supervisor: ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->with('error', 'Error al crear supervisor: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar supervisor
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Supervisor::findOrFail($id)->user_id,
            'max_estudiantes' => 'required|integer|min:1|max:100',
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'email.required' => 'El correo es obligatorio',
            'email.unique' => 'Este correo ya está registrado',
            'max_estudiantes.required' => 'La capacidad máxima es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
        ]);

        try {
            \DB::beginTransaction();

            $supervisor = Supervisor::with('user')->findOrFail($id);
            $user = $supervisor->user;

            // Actualizar usuario
            $user->name = $request->nombre;
            $user->email = $request->email;
            $user->cod_rol = 3;  // ✅ Asegurar que siempre sea 3
            
            // Solo actualizar contraseña si se proporciona
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            
            $user->save();

            // Actualizar supervisor
            $supervisor->max_estudiantes = $request->max_estudiantes;
            $supervisor->save();

            \DB::commit();

            Log::info('Supervisor actualizado: ' . $user->name . ' (ID: ' . $supervisor->id . ')');

            return redirect()
                ->route('admin.supervisores.index')
                ->with('success', 'Supervisor ' . $user->name . ' actualizado exitosamente');

        } catch (\Exception $e) {
            \DB::rollBack();
            
            Log::error('Error al actualizar supervisor: ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->with('error', 'Error al actualizar supervisor: ' . $e->getMessage());
        }
    }
        /**
     * Activar/Desactivar supervisor
     */
    public function toggleActivo($id)
    {
        try {
            $supervisor = Supervisor::with('user')->findOrFail($id);
            
            // Verificar si tiene estudiantes asignados antes de desactivar
            if ($supervisor->activo && $supervisor->estudiantes_asignados > 0) {
                return back()->with('error', 'No se puede desactivar un supervisor con estudiantes asignados actualmente');
            }

            $supervisor->activo = !$supervisor->activo;
            $supervisor->save();

            $estado = $supervisor->activo ? 'activado' : 'desactivado';
            
            Log::info('Supervisor ' . $estado . ': ' . $supervisor->user->name);

            return redirect()
                ->route('admin.supervisores.index')
                ->with('success', 'Supervisor ' . $supervisor->user->name . ' ' . $estado . ' exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al cambiar estado de supervisor: ' . $e->getMessage());
            
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar supervisor (soft delete recomendado)
     */
    public function destroy($id)
    {
        try {
            $supervisor = Supervisor::with('user')->findOrFail($id);

            // Verificar si tiene estudiantes asignados
            if ($supervisor->estudiantes_asignados > 0) {
                return back()->with('error', 'No se puede eliminar un supervisor con estudiantes asignados');
            }

            $nombreSupervisor = $supervisor->user->name;

            // Desactivar en lugar de eliminar (recomendado)
            $supervisor->activo = 0;
            $supervisor->save();

            Log::info('Supervisor desactivado (eliminación suave): ' . $nombreSupervisor);

            return redirect()
                ->route('admin.supervisores.index')
                ->with('success', 'Supervisor ' . $nombreSupervisor . ' eliminado exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al eliminar supervisor: ' . $e->getMessage());
            
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}