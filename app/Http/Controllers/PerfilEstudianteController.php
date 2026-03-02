<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PerfilEstudianteController extends Controller
{
    public function edit()
    {
        return view('estudiantes.perfil', [
            'user' => Auth::user()
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'foto' => 'nullable|image|max:2048',
        ]);

        $user = Auth::user();
        $user->name = $request->name;

        if ($request->hasFile('foto')) {
            $foto = $request->file('foto')->store('fotos/fotos_perfil', 'public');
            $user->foto = $foto;
        }

        $user->save();

        return redirect()->route('estudiantes.perfil')->with('success', 'Perfil actualizado correctamente.');
    }
}
