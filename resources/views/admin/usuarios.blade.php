@extends('layouts.admin')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-6">Gestión de Usuarios</h1>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- AGREGAR USUARIO INDIVIDUAL --}}
    <div class="bg-white shadow rounded-xl p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Agregar Usuario</h2>
        <form action="{{ route('admin.usuarios.store') }}" method="POST" class="flex flex-wrap gap-4">
            @csrf
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre completo</label>
                <input type="text" name="name" required class="w-full border rounded px-3 py-2 text-sm">
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
                <input type="email" name="email" required class="w-full border rounded px-3 py-2 text-sm">
            </div>
            <div class="w-48">
                <label class="block text-sm font-medium text-gray-700 mb-1">Rol</label>
                <select name="rol" required class="w-full border rounded px-3 py-2 text-sm">
                    <option value="estudiante">Estudiante</option>
                    <option value="supervisor">Supervisor</option>
                    <option value="admin">Admin</option>
                    <option value="vinculacion">Vinculacion</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                    Crear y enviar correo
                </button>
            </div>
        </form>
    </div>

    {{-- IMPORTAR EXCEL --}}
    <div class="bg-white shadow rounded-xl p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">Importar usuarios desde Excel</h2>
        <p class="text-sm text-gray-500 mb-3">El archivo debe tener columnas: <strong>nombre</strong>, <strong>correo</strong>, <strong>rol</strong> (en ese orden, con encabezado en fila 1)</p>
        <form action="{{ route('admin.usuarios.importar') }}" method="POST" enctype="multipart/form-data" class="flex items-end gap-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Archivo Excel (.xlsx, .xls, .csv)</label>
                <input type="file" name="archivo" accept=".xlsx,.xls,.csv" required class="text-sm">
            </div>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm">
                Importar
            </button>
        </form>
    </div>

    {{-- LISTADO DE USUARIOS --}}
    <div class="bg-white shadow rounded-xl p-6">
        <h2 class="text-lg font-semibold mb-4">Usuarios registrados</h2>
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="px-4 py-2">Nombre</th>
                    <th class="px-4 py-2">Correo</th>
                    <th class="px-4 py-2">Rol actual</th>
                    <th class="px-4 py-2">Verificado</th>
                    <th class="px-4 py-2">Cambiar rol</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($usuarios as $usuario)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $usuario->name }}</td>
                    <td class="px-4 py-2">{{ $usuario->email }}</td>
                    <td class="px-4 py-2">{{ ucfirst($usuario->rol ?? 'Sin rol') }}</td>
                    <td class="px-4 py-2">
                        @if ($usuario->email_verified_at)
                            <span class="text-green-600 font-semibold">Sí</span>
                        @else
                            <span class="text-red-500">No</span>
                        @endif
                    </td>
                    <td class="px-4 py-2">
                        <form action="{{ route('admin.usuarios.updateRol', $usuario->id) }}" method="POST" class="flex gap-2">
                            @csrf
                            @method('PUT')
                            <select name="rol" class="border rounded px-2 py-1 text-sm">
                                <option value="estudiante" {{ $usuario->rol === 'estudiante' ? 'selected' : '' }}>Estudiante</option>
                                <option value="supervisor" {{ $usuario->rol === 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                                <option value="admin" {{ $usuario->rol === 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="vinculacion" {{ $usuario->rol === 'vinculacion' ? 'selected' : '' }}>Vinculacion</option>
                            </select>
                            <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded text-sm">
                                Actualizar
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection