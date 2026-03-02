@extends('layouts.admin')



@section('content')
    <h1>Solicitudes a Práctica Profesional</h1>
@stop

@section('content')
    <div class="mb-4">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin/solicitudes/pendientes') ? 'active' : '' }}" href="{{ route('admin.solicitudes.pendientes') }}">🕓 Pendientes</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin/solicitudes/aprobadas') ? 'active' : '' }}" href="{{ route('admin.solicitudes.aprobadas') }}">✅ Aprobadas</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin/solicitudes/canceladas') ? 'active' : '' }}" href="{{ route('admin.solicitudes.canceladas') }}">❌ Canceladas</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('admin/solicitudes/finalizadas') ? 'active' : '' }}" href="{{ route('admin.solicitudes.finalizadas') }}">🏁 Finalizadas</a>
            </li>
        </ul>
    </div>

    <div class="alert alert-info">
        Selecciona una categoría para ver las solicitudes según su estado.
    </div>
@endsection
