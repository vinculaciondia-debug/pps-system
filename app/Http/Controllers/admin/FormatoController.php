<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Formato;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class FormatoController extends Controller
{
    /**
     * Mostrar listado de formatos (admin)
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', config('pagination.per_page', 15));
        $q       = trim((string) $request->query('q', ''));
        $tipo    = $request->query('tipo');         // 'pdf' | 'word'
        $visible = $request->query('visible');      // '1' | '0'

        $query = Formato::query();

        if ($q !== '') {
            $query->where('nombre', 'like', "%{$q}%");
        }

        if (in_array($tipo, ['pdf', 'word'], true)) {
            $query->where('tipo', $tipo);
        }

        if ($visible !== null && in_array($visible, ['0','1'], true)) {
            $query->where('visible', (int) $visible);
        }

        $formatos = $query->latest('id')->paginate($perPage)->withQueryString();

        return view('admin.formatos.index', compact('formatos'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        return view('admin.formatos.create');
    }

    /**
     * Guardar nuevo formato
     */
    public function store(Request $request)
    {
        Log::info('=== INICIO CREACIÓN FORMATO ===');
        
        try {
            // Validar datos
            $validated = $request->validate([
                'nombre' => 'required|string|max:255|unique:formatos,nombre',
                'archivo' => 'required|file|mimes:pdf,doc,docx|max:10240',
                'visible' => 'nullable',
                'tipo_destinatario' => 'required|in:estudiante,supervisor',
            ], [
                'nombre.required' => 'El nombre es obligatorio',
                'nombre.unique' => 'Ya existe un formato con ese nombre',
                'archivo.required' => 'Debes seleccionar un archivo',
                'archivo.mimes' => 'Solo se permiten archivos PDF, DOC o DOCX',
                'archivo.max' => 'El archivo no puede superar los 10 MB',
                'tipo_destinatario.required' => 'Debes seleccionar el tipo de destinatario',
            ]);

            Log::info('Validación pasada');

            // Crear directorio si no existe
            $directorioFormatos = public_path('formatos');
            if (!file_exists($directorioFormatos)) {
                mkdir($directorioFormatos, 0755, true);
            }

            // Subir archivo
            $file = $request->file('archivo');
            $extension = $file->getClientOriginalExtension();
            $nombreArchivo = Str::random(40) . '.' . $extension;
            
            $file->move($directorioFormatos, $nombreArchivo);
            
            Log::info('Archivo movido exitosamente: ' . $nombreArchivo);

            // Determinar tipo
            $tipo = $extension === 'pdf' ? 'pdf' : 'word';

            // Crear registro
            $formato = Formato::create([
                'nombre' => $request->nombre,
                'ruta' => 'formatos/' . $nombreArchivo,
                'tipo' => $tipo,
                'visible' => $request->has('visible') ? 1 : 0,
                'tipo_destinatario' => $request->tipo_destinatario,
            ]);

            Log::info('Formato creado con ID: ' . $formato->id);

            return redirect()->route('admin.formatos.index')
                ->with('success', 'Formato creado exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al crear formato: ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $formato = Formato::findOrFail($id);
        return view('admin.formatos.edit', compact('formato'));
    }

    /**
     * Actualizar formato
     */
    public function update(Request $request, $id)
    {
        Log::info('=== INICIO ACTUALIZACIÓN FORMATO #' . $id . ' ===');
        Log::info('Datos recibidos:', $request->all());

        try {
            $formato = Formato::findOrFail($id);

            // Validar (nombre puede ser el mismo si es el formato actual)
            $request->validate([
                'nombre' => 'required|string|max:255|unique:formatos,nombre,' . $id,
                'archivo' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
                'visible' => 'nullable',
                'tipo_destinatario' => 'required|in:estudiante,supervisor',
            ], [
                'nombre.required' => 'El nombre es obligatorio',
                'nombre.unique' => 'Ya existe otro formato con ese nombre',
                'archivo.mimes' => 'Solo se permiten archivos PDF, DOC o DOCX',
                'archivo.max' => 'El archivo no puede superar los 10 MB',
                'tipo_destinatario.required' => 'Debes seleccionar el tipo de destinatario',
            ]);

            Log::info('Validación pasada');

            // Si se sube nuevo archivo
            if ($request->hasFile('archivo')) {
                Log::info('📤 Subiendo nuevo archivo...');

                // Eliminar archivo anterior
                if (file_exists(public_path($formato->ruta))) {
                    unlink(public_path($formato->ruta));
                    Log::info('🗑️ Archivo anterior eliminado');
                }

                // Subir nuevo archivo
                $file = $request->file('archivo');
                $extension = $file->getClientOriginalExtension();
                $nombreArchivo = Str::random(40) . '.' . $extension;
                
                $directorioFormatos = public_path('formatos');
                if (!file_exists($directorioFormatos)) {
                    mkdir($directorioFormatos, 0755, true);
                }

                $file->move($directorioFormatos, $nombreArchivo);
                
                Log::info('Nuevo archivo guardado: ' . $nombreArchivo);

                $formato->ruta = 'formatos/' . $nombreArchivo;
                $formato->tipo = $extension === 'pdf' ? 'pdf' : 'word';
            }

            // Actualizar datos
            $formato->nombre = $request->nombre;
            $formato->visible = $request->has('visible') ? 1 : 0;
            $formato->tipo_destinatario = $request->tipo_destinatario;
            $formato->save();

            Log::info('Formato actualizado correctamente');

            return redirect()->route('admin.formatos.index')
                ->with('success', 'Formato actualizado exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al actualizar formato: ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar formato
     */
    public function destroy($id)
    {
        try {
            $formato = Formato::findOrFail($id);

            // Eliminar archivo físico
            if (file_exists(public_path($formato->ruta))) {
                unlink(public_path($formato->ruta));
                Log::info('Archivo eliminado: ' . $formato->ruta);
            }

            // Eliminar registro
            $formato->delete();
            Log::info('Formato eliminado de BD con ID: ' . $id);

            return redirect()->route('admin.formatos.index')
                ->with('success', 'Formato eliminado exitosamente');

        } catch (\Exception $e) {
            Log::error('Error al eliminar formato: ' . $e->getMessage());
            
            return back()
                ->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }

    /**
     * Descargar formato
     */
    public function download($id)
    {
        try {
            $formato = Formato::findOrFail($id);
            $rutaCompleta = public_path($formato->ruta);

            if (!file_exists($rutaCompleta)) {
                Log::error('Archivo no encontrado: ' . $rutaCompleta);
                return redirect()
                    ->back()
                    ->with('error', 'El archivo no existe en el servidor');
            }

            $extension = pathinfo($formato->ruta, PATHINFO_EXTENSION);
            $nombreDescarga = $formato->nombre . '.' . $extension;

            Log::info('Descargando archivo: ' . $nombreDescarga);

            return response()->download($rutaCompleta, $nombreDescarga);

        } catch (\Exception $e) {
            Log::error('Error al descargar: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'Error al descargar: ' . $e->getMessage());
        }
    }
}