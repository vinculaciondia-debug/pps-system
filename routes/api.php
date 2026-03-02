<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ==================== IMPORTS CONTROLADORES (deben ir ANTES de las rutas) ==================== //

use App\Http\Controllers\Api\v1\Student\DashboardApiController;
use App\Http\Controllers\Api\v1\Student\SolicitudController;
// ⬅️ Renombramos para evitar conflicto con App\Http\Controllers\DocumentoController
use App\Http\Controllers\Api\v1\Student\DocumentoController as DocumentoApiController;
use App\Http\Controllers\Api\v1\DocumentoDescargaController;
use App\Http\Controllers\Api\v1\FormatoController;
use App\Http\Controllers\Api\v1\MeController;

// ==================== RUTA POR DEFECTO (sanctum) ==================== //

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// ==================== RUTAS API v1 (PROTEGIDAS CON SANCTUM) ==================== //

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {

    // Dashboard del estudiante
    Route::get('/student/dashboard', [DashboardApiController::class, 'index']);
    
    // 👉 NUEVO: Dashboard alternativo desde SolicitudController
    Route::get('/student/dashboard2', [SolicitudController::class, 'dashboard']);

    // Solicitudes del estudiante
    Route::get('/solicitudes/mias', [SolicitudController::class, 'mias']); // ?solo_activa=true
    Route::post('/solicitudes', [SolicitudController::class, 'store']);    // crear nueva → SOLICITADA

    Route::post('/solicitudes/{id}/cancelar', [SolicitudController::class, 'cancelar'])
        ->whereNumber('id'); // defensa extra

    Route::get('/solicitudes/{id}', [SolicitudController::class, 'show'])
        ->whereNumber('id');

    // Documentos ligados a una solicitud (USAMOS DocumentoApiController)
    Route::get('/solicitudes/{id}/documentos', [DocumentoApiController::class, 'index'])
        ->whereNumber('id');

    Route::post('/solicitudes/{id}/documentos', [DocumentoApiController::class, 'store'])
        ->whereNumber('id');

    // Ver y descargar documentos (con Policy)
    Route::get('/documentos/{id}/view', [DocumentoDescargaController::class, 'view'])
        ->whereNumber('id')
        ->name('documentos.view');

    Route::get('/documentos/{id}/download', [DocumentoDescargaController::class, 'download'])
        ->whereNumber('id')
        ->name('documentos.download');

    // Formatos disponibles (lista y descarga)
    Route::get('/formatos', [FormatoController::class, 'index']);

    Route::get('/formatos/{id}/download', [FormatoController::class, 'download'])
        ->whereNumber('id')
        ->name('api.v1.formatos.download'); // <- ya corregido el punto y coma

    // Acción rápida “Actualizar datos” del perfil
    Route::patch('/me', [MeController::class, 'update']);
});
