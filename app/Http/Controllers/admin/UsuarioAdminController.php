<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Spatie\Permission\Models\Role;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UsuarioAdminController extends Controller
{
    // Mostrar listado + formularios
    public function index()
    {
        $usuarios = User::with('roles')->latest()->get();
        $roles = Role::all();
      return view('admin.usuarios', compact('usuarios', 'roles'));
    }

    // Crear un usuario individual
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'rol'   => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make(\Str::random(16)),
            'rol'      => $request->rol,
        ]);

        

        event(new Registered($user));

        return back()->with('success', "Usuario {$user->name} creado. Se le envió un correo de verificación.");
    }

    // Importar usuarios desde Excel
    public function importar(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        $archivo = $request->file('archivo');
        $spreadsheet = IOFactory::load($archivo->getPathname());
        $filas = $spreadsheet->getActiveSheet()->toArray();

        $creados = 0;
        $errores = [];

        foreach ($filas as $index => $fila) {
            if ($index === 0) continue; // Saltar encabezado

            $name  = trim($fila[0] ?? '');
            $email = trim($fila[1] ?? '');
            $rol   = trim($fila[2] ?? 'estudiante');

            if (!$name || !$email) continue;

            if (User::where('email', $email)->exists()) {
                $errores[] = "El correo {$email} ya existe.";
                continue;
            }

            if (!Role::where('name', $rol)->exists()) {
                $errores[] = "El rol '{$rol}' no es válido para {$email}.";
                continue;
            }

            $user = User::create([
                'name'     => $name,
                'email'    => $email,
                'password' => Hash::make(\Str::random(16)),
                'rol'      => $rol,
            ]);

            $user->assignRole($rol);
            event(new Registered($user));
            $creados++;
        }

        $mensaje = "{$creados} usuarios creados exitosamente.";
        if ($errores) {
            $mensaje .= " Errores: " . implode(' | ', $errores);
        }

        return back()->with('success', $mensaje);
    }

    // Actualizar rol
    public function updateRol(Request $request, User $user)
    {
        $request->validate([
            'rol' => 'required|exists:roles,name',
        ]);

      $user->rol = $request->rol;
$user->save();
        return back()->with('success', 'Rol actualizado correctamente.');
    }
}