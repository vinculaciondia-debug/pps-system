<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    // Mostrar listado de usuarios con sus roles
    public function index()
    {
        $usuarios = User::with('roles')->get();
        $roles = Role::all();

        return view('admin.usuarios.index', compact('usuarios', 'roles'));
    }

    // Actualizar el rol de un usuario
    public function updateRol(Request $request, User $user)
    {
        $request->validate([
            'rol' => 'required|exists:roles,name',
        ]);

        $user->syncRoles([$request->rol]);

        return back()->with('success', 'Rol actualizado correctamente.');
    }
}
