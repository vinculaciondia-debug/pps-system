<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    public function index(Request $request)
    {
        $query = Empresa::withCount(['solicitudes as total_estudiantes']);

        if ($request->filled('busqueda')) {
            $query->where('nombre', 'like', '%' . $request->busqueda . '%');
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('estado')) {
            $query->where('activa', $request->estado === 'activa' ? 1 : 0);
        }

        $empresas = $query->orderBy('nombre')->paginate(20)->withQueryString();

        return view('admin.empresas.index', compact('empresas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'    => 'required|string|max:255|unique:empresas,nombre',
            'tipo'      => 'nullable|in:publica,privada',
            'direccion' => 'nullable|string|max:500',
        ], [
            'nombre.required' => 'El nombre de la empresa es obligatorio',
            'nombre.unique'   => 'Ya existe una empresa con ese nombre',
        ]);

        Empresa::create([
            'nombre'    => $request->nombre,
            'tipo'      => $request->tipo ?: null,
            'direccion' => $request->direccion ?: null,
            'activa'    => true,
        ]);

        return back()->with('success', 'Empresa creada correctamente.');
    }

    public function update(Request $request, Empresa $empresa)
    {
        $request->validate([
            'nombre'    => 'required|string|max:255|unique:empresas,nombre,' . $empresa->id,
            'tipo'      => 'nullable|in:publica,privada',
            'direccion' => 'nullable|string|max:500',
        ], [
            'nombre.required' => 'El nombre de la empresa es obligatorio',
            'nombre.unique'   => 'Ya existe una empresa con ese nombre',
        ]);

        $empresa->update([
            'nombre'    => $request->nombre,
            'tipo'      => $request->tipo ?: null,
            'direccion' => $request->direccion ?: null,
        ]);

        return back()->with('success', 'Empresa actualizada correctamente.');
    }

    public function toggleActiva(Empresa $empresa)
    {
        $empresa->update(['activa' => !$empresa->activa]);

        $estado = $empresa->activa ? 'activada' : 'desactivada';
        return back()->with('success', "Empresa {$estado} correctamente.");
    }

    public function destroy(Empresa $empresa)
    {
        if ($empresa->solicitudes()->count() > 0) {
            return back()->with('error', 'No se puede eliminar una empresa que tiene prácticas registradas.');
        }

        $empresa->delete();
        return back()->with('success', 'Empresa eliminada correctamente.');
    }
}
