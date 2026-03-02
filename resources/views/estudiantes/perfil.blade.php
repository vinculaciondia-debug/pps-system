@extends('layouts.estudiantes')

@section('content')

<div class="max-w-3xl mx-auto mt-10 bg-white p-6 rounded shadow">

    <h1 class="text-2xl font-bold mb-6 text-unahblue">Perfil del Estudiante</h1>

    <!-- Mostrar mensaje de éxito -->
    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(auth()->user()->foto)
        <div class="mb-4">
            <p class="font-semibold mb-2">Foto actual:</p>
            <!-- 📌 Foto reducida a 70x70 px -->
            <img src="{{ asset('storage/' . auth()->user()->foto) }}" 
                 alt="Foto de perfil" 
                 class="h-18 w-18 object-cover rounded-full border border-gray-300 shadow">
        </div>
    @endif

    <form action="{{ route('estudiantes.perfil.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Nombre -->
        <div class="mb-4">
            <x-label value="Nombre completo" />
            <x-input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required />
        </div>

        <!-- Email solo lectura -->
        <div class="mb-4">
            <x-label value="Correo institucional" />
            <x-input type="email" name="email" value="{{ auth()->user()->email }}" readonly class="bg-gray-100 cursor-not-allowed" />
        </div>

        <!-- Subir nueva foto -->
        <div class="mb-4">
            <x-label value="Cambiar foto de perfil (opcional)" />
            <input type="file" name="foto" accept="image/*" class="mt-1 block w-full border rounded p-2 text-sm">
        </div>

        <div class="mt-6">
            <button type="submit" class="bg-unahblue hover:bg-unahgold text-white px-6 py-2 rounded">
                Guardar cambios
            </button>
        </div>
    </form>

</div>

@endsection
