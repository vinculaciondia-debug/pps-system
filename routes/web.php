<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\PracticaAdminController;
use App\Http\Controllers\SolicitudPPSController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SolicitudActualizacionController;
use App\Http\Controllers\SolicitudCancelacionController;
use App\Http\Controllers\PerfilEstudianteController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\FormatoController;
use App\Http\Controllers\Admin\ReporteController;
use App\Http\Controllers\supervisor\FormatoSupervisionController;

// Página de bienvenida
Route::get('/', function () {
    return view('welcome');
});

// Redirección al dashboard según el rol del usuario
// Redirección al dashboard según el rol del usuario
Route::get('/dashboard', function () {
    $user = auth()->user();

    // Verificar si es admin o vinculacion Y también supervisor
    $esSupervisor = \App\Models\Supervisor::where('user_id', $user->id)->exists();
    
    if ($user->rol === 'admin') {
        if ($esSupervisor) {
            return view('dashboard-selector', ['roles' => ['admin', 'supervisor']]);
        }
        return redirect()->route('admin.dashboard');
    } 
    
    if ($user->rol === 'vinculacion') {
        if ($esSupervisor) {
            return view('dashboard-selector', ['roles' => ['vinculacion', 'supervisor']]);
        }
        // Si tienes dashboard de vinculación, usa esta ruta:
        // return redirect()->route('vinculacion.dashboard');
        
        // Si NO tienes dashboard de vinculación, redirige al admin:
        return redirect()->route('admin.dashboard');
    }
    
    if ($user->rol === 'estudiante') {
        return redirect()->route('estudiantes.dashboard');
    } 
    
    if ($user->rol === 'supervisor') {
        return redirect()->route('supervisor.dashboard');
    }

    abort(403, 'Rol no válido');
})->middleware(['auth', 'verified'])->name('dashboard');

// ======================= ADMIN =======================
Route::middleware(['auth', 'verified', 'admin.role'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/reportes', [\App\Http\Controllers\Admin\DashboardController::class, 'reportes'])->name('dashboard.reportes');
        Route::get('/dashboard/exportar', [\App\Http\Controllers\Admin\DashboardController::class, 'exportarReporte'])->name('dashboard.exportar');
        // ========================================
        // GESTIÓN DE SOLICITUDES (MÓDULO COMPLETO)
        // ========================================
        
        // Vistas de solicitudes por estado
        Route::get('/solicitudes/pendientes', [\App\Http\Controllers\Admin\SolicitudAdminController::class, 'pendientes'])
            ->name('solicitudes.pendientes');
        Route::get('/solicitudes/aprobadas', [\App\Http\Controllers\Admin\SolicitudAdminController::class, 'aprobadas'])
            ->name('solicitudes.aprobadas');
        Route::get('/solicitudes/rechazadas', [\App\Http\Controllers\Admin\SolicitudAdminController::class, 'rechazadas'])
            ->name('solicitudes.rechazadas');
        Route::get('/solicitudes/finalizadas', [\App\Http\Controllers\Admin\SolicitudAdminController::class, 'finalizadas'])
            ->name('solicitudes.finalizadas');
        Route::get('/solicitudes/canceladas', [\App\Http\Controllers\Admin\SolicitudAdminController::class, 'canceladas'])
            ->name('solicitudes.canceladas');
        
        // Ver detalle y documentos
        Route::get('/solicitudes/{id}', [\App\Http\Controllers\Admin\SolicitudAdminController::class, 'show'])
            ->whereNumber('id')->name('solicitudes.show');
        Route::get('/solicitudes/{id}/foto', [\App\Http\Controllers\Admin\SolicitudAdminController::class, 'verFoto'])
            ->whereNumber('id')->name('solicitud.foto');
        Route::get('/solicitudes/documentos/{id}', [\App\Http\Controllers\Admin\SolicitudAdminController::class, 'verDocumentos'])
            ->whereNumber('id')->name('solicitudes.documentos');
        
        // Acciones sobre solicitudes
        Route::post('/solicitudes/{id}/aprobar', [\App\Http\Controllers\Admin\SolicitudAdminController::class, 'aprobar'])
            ->whereNumber('id')->name('solicitudes.aprobar');
        Route::post('/solicitudes/{id}/rechazar', [\App\Http\Controllers\Admin\SolicitudAdminController::class, 'rechazar'])
            ->whereNumber('id')->name('solicitudes.rechazar');
        Route::post('/solicitudes/{id}/cambiar-supervisor', [\App\Http\Controllers\Admin\SolicitudAdminController::class, 'cambiarSupervisor'])
            ->whereNumber('id')->name('solicitudes.cambiar-supervisor');
        Route::get('/admin/solicitudes/{id}/foto', [\App\Http\Controllers\Admin\SolicitudAdminController::class, 'verFoto'])
            ->name('admin.solicitud.foto');

        // FINALIZAR PRÁCTICA 
        Route::patch('/solicitudes/{id}/finalizar', [\App\Http\Controllers\Admin\SolicitudAdminController::class, 'finalizar'])
            ->whereNumber('id')->name('solicitudes.finalizar');
        
        // 🆕 GESTIÓN DE CARTA DE FINALIZACIÓN (APROBAR/RECHAZAR)
        Route::patch('/solicitudes/{solicitud}/carta-finalizacion/{documento}/rechazar', 
            [\App\Http\Controllers\Admin\SolicitudAdminController::class, 'rechazarCartaFinalizacion'])
            ->name('solicitudes.carta.rechazar');
        
        Route::patch('/solicitudes/{solicitud}/carta-finalizacion/{documento}/aprobar', 
            [\App\Http\Controllers\Admin\SolicitudAdminController::class, 'aprobarCartaFinalizacion'])
            ->name('solicitudes.carta.aprobar');
        
        // Supervisores disponibles
        Route::get('/supervisores/disponibles', [\App\Http\Controllers\Admin\SolicitudAdminController::class, 'getSupervisoresDisponibles'])
            ->name('supervisores.disponibles');
        
        // Descargar supervisión
        Route::get('/supervisiones/{id}/descargar', [\App\Http\Controllers\Admin\SolicitudAdminController::class, 'descargarSupervision'])
            ->whereNumber('id')->name('supervisiones.descargar');
        Route::get('/supervisiones/{id}/ver', [\App\Http\Controllers\Admin\SolicitudAdminController::class, 'verSupervision'])
            ->whereNumber('id')->name('supervisiones.ver');
        // ========================================
        // GESTIÓN DE SUPERVISORES (ADMIN)
        // ========================================
        Route::prefix('supervisores')->name('supervisores.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\SupervisorController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\Admin\SupervisorController::class, 'store'])->name('store');
            Route::get('/{id}', [\App\Http\Controllers\Admin\SupervisorController::class, 'show'])->name('show')->whereNumber('id');
            Route::put('/{id}', [\App\Http\Controllers\Admin\SupervisorController::class, 'update'])->name('update')->whereNumber('id');
            Route::patch('/{id}/toggle', [\App\Http\Controllers\Admin\SupervisorController::class, 'toggleActivo'])->name('toggle')->whereNumber('id');
            Route::delete('/{id}', [\App\Http\Controllers\Admin\SupervisorController::class, 'destroy'])->name('destroy')->whereNumber('id');
        });
        
        // ========================================
        // GESTIÓN DE FORMATOS 
        // ========================================
        Route::get('/formatos', [\App\Http\Controllers\Admin\FormatoController::class, 'index'])
            ->name('formatos.index');
        Route::get('/formatos/crear', [\App\Http\Controllers\Admin\FormatoController::class, 'create'])
            ->name('formatos.create');
        Route::post('/formatos', [\App\Http\Controllers\Admin\FormatoController::class, 'store'])
            ->name('formatos.store');
        Route::get('/formatos/{id}/editar', [\App\Http\Controllers\Admin\FormatoController::class, 'edit'])
            ->name('formatos.edit');
        Route::put('/formatos/{id}', [\App\Http\Controllers\Admin\FormatoController::class, 'update'])
            ->name('formatos.update');
        Route::delete('/formatos/{id}', [\App\Http\Controllers\Admin\FormatoController::class, 'destroy'])
            ->name('formatos.destroy');
        
        Route::get('/formatos/{id}/descargar', [\App\Http\Controllers\Admin\FormatoController::class, 'download'])
            ->whereNumber('id')->name('formatos.download');

       // ========================================
// GESTIÓN DE USUARIOS
// ========================================
Route::get('/usuarios', [\App\Http\Controllers\Admin\UsuarioAdminController::class, 'index'])->name('usuarios.index');
Route::post('/usuarios', [\App\Http\Controllers\Admin\UsuarioAdminController::class, 'store'])->name('usuarios.store');
Route::post('/usuarios/importar', [\App\Http\Controllers\Admin\UsuarioAdminController::class, 'importar'])->name('usuarios.importar');
Route::put('/usuarios/{user}/rol', [\App\Http\Controllers\Admin\UsuarioAdminController::class, 'updateRol'])->whereNumber('user')->name('usuarios.updateRol');

        // ========================================
        // SOLICITUDES DE ACTUALIZACIÓN (backoffice)
        // ========================================
        Route::get('/actualizacion', [SolicitudActualizacionController::class, 'index'])
            ->name('solicitudes.actualizacion');
        Route::get('/actualizacion/{id}/archivo', [SolicitudActualizacionController::class, 'verArchivo'])
            ->whereNumber('id')->name('actualizacion.ver-archivo');
        Route::put('/actualizacion/{id}', [SolicitudActualizacionController::class, 'update'])
            ->whereNumber('id')->name('solicitudes.actualizacion.update');

        // ========================================
        // OTRAS VISTAS
        // ========================================
        Route::get('/audit-log', [\App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('audit.index');

        // ========================================
        // GESTIÓN DE EMPRESAS
        // ========================================
        Route::prefix('empresas')->name('empresas.')->group(function () {
            Route::get('/',                    [\App\Http\Controllers\Admin\EmpresaController::class, 'index'])->name('index');
            Route::post('/',                   [\App\Http\Controllers\Admin\EmpresaController::class, 'store'])->name('store');
            Route::put('/{empresa}',           [\App\Http\Controllers\Admin\EmpresaController::class, 'update'])->name('update');
            Route::patch('/{empresa}/toggle',  [\App\Http\Controllers\Admin\EmpresaController::class, 'toggleActiva'])->name('toggle');
            Route::delete('/{empresa}',        [\App\Http\Controllers\Admin\EmpresaController::class, 'destroy'])->name('destroy');
        });

        Route::view('/reportes', 'admin.reportes')->name('reportes');
        Route::get('reportes', [ReporteController::class,'index'])->name('reportes');
        Route::post('reportes/preview', [ReporteController::class,'preview'])->name('reportes.preview');
        Route::get('reportes/export/pdf', [ReporteController::class,'exportPdf'])->name('reportes.export.pdf');
        Route::get('reportes/export/excel', [ReporteController::class,'exportExcel'])->name('reportes.export.excel');
    });

// =================== ESTUDIANTES ===================
Route::middleware(['auth', 'verified', 'estudiante.acceso'])
    ->prefix('estudiantes')
    ->name('estudiantes.')
    ->group(function () {
        // Dashboard estudiante
        Route::get('/dashboard', function () {
            $user = auth()->user();

            //  Última solicitud (incluyendo rechazadas y canceladas para mostrar mensajes)
            $solicitud = \App\Models\SolicitudPPS::delUsuario($user->id)
                ->with(['documentos', 'supervisor'])  //  Cargar supervisor
                ->latest('id')
                ->first();

            return view('estudiantes.dashboard', compact('solicitud')); //  Solo solicitud
                })->name('dashboard');

        // Solicitud de práctica
        Route::get('/solicitud',  [SolicitudPPSController::class, 'create'])->name('solicitud');
        Route::post('/solicitud', [SolicitudPPSController::class, 'store'])->name('solicitud.store');
        Route::post('/solicitud/calcular-fecha', [SolicitudPPSController::class, 'calcularFechaFinalizacion'])
    ->name('solicitud.calcular-fecha');


        // =================== EMPRESAS (Autocomplete) ===================
Route::get('/empresas/buscar', [\App\Http\Controllers\EmpresaLookupController::class, 'search'])
    ->name('empresas.search');

Route::post('/empresas', [\App\Http\Controllers\EmpresaLookupController::class, 'store'])
    ->name('empresas.store');
        // Formatos
        Route::get('/formatos', [FormatoController::class, 'index'])->name('formatos');
        Route::get('/formatos/{id}/download', [FormatoController::class, 'download'])
            ->whereNumber('id')->name('formatos.download');
        Route::get('/formatos/{id}/view', [FormatoController::class, 'view']) 
            ->whereNumber('id')->name('formatos.view');

        // Documentos
        Route::get('/documentos',                [DocumentoController::class, 'index'])->name('documentos.index');
        Route::post('/documentos',               [DocumentoController::class, 'store'])->name('documentos.guardar');
        Route::get('/documentos/{id}/ver',       [DocumentoController::class, 'ver'])->whereNumber('id')->name('documentos.ver');
        Route::get('/documentos/{id}/descargar', [DocumentoController::class, 'descargar'])->whereNumber('id')->name('documentos.descargar');
        Route::delete('/documentos/{documento}', [DocumentoController::class, 'destroy'])->whereNumber('documento')->name('documentos.eliminar');

        //  Ver documentos de una solicitud
        Route::get('/solicitudes/{id}/documentos', [SolicitudPPSController::class, 'verDocumentos'])
            ->whereNumber('id')->name('solicitudes.documentos');

        // Actualización de datos de la solicitud
        Route::patch('/solicitudes/{id}', [SolicitudPPSController::class, 'update'])
            ->whereNumber('id')->name('solicitudes.update');

        // Actualización de datos (flujo adicional que ya tenías)
        Route::get('/actualizacion',  [SolicitudActualizacionController::class, 'create'])->name('actualizacion.create');
        Route::post('/actualizacion', [SolicitudActualizacionController::class, 'store'])->name('actualizacion.store');

        // Cancelación de práctica (soporta sin id y con id)
        Route::get('/cancelacion/{id?}',  [SolicitudCancelacionController::class, 'create'])
            ->whereNumber('id')->name('cancelacion.create');
        Route::post('/cancelacion/{id?}', [SolicitudCancelacionController::class, 'cancelar'])
            ->whereNumber('id')->name('cancelacion.store');

        // Perfil
        Route::get('/perfil',  [PerfilEstudianteController::class, 'edit'])->name('perfil');
        Route::put('/perfil',  [PerfilEstudianteController::class, 'update'])->name('perfil.update');
    });

// ======================= SUPERVISOR =======================
Route::middleware(['auth', 'verified', 'supervisor'])
    ->prefix('supervisor')
    ->name('supervisor.')
    ->group(function () {
        
        Route::get('/dashboard', [\App\Http\Controllers\Supervisor\DashboardController::class, 'index'])->name('dashboard');
        
        Route::get('/alumnos/{solicitud}/formatos/supervision/{numero}',
            [FormatoSupervisionController::class, 'downloadDocx'])
            ->whereNumber('numero')
            ->name('alumnos.formatos.supervision');
        // ========================================
        // GESTIÓN DE ALUMNOS ASIGNADOS
        // ========================================
        Route::prefix('alumnos')->name('alumnos.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Supervisor\AlumnoController::class, 'index'])->name('index');
            Route::get('/{id}', [\App\Http\Controllers\Supervisor\AlumnoController::class, 'show'])->name('show')->whereNumber('id');
            Route::get('/{id}/datos', [\App\Http\Controllers\Supervisor\AlumnoController::class, 'obtenerDatos'])->name('datos')->whereNumber('id');
            Route::post('/{id}/supervision', [\App\Http\Controllers\Supervisor\AlumnoController::class, 'subirSupervision'])->name('supervision.subir')->whereNumber('id');
            Route::get('/supervision/{id}/descargar', [\App\Http\Controllers\Supervisor\AlumnoController::class, 'descargarSupervision'])->name('supervision.descargar')->whereNumber('id');
            Route::delete('/supervision/{id}', [\App\Http\Controllers\Supervisor\AlumnoController::class, 'eliminarSupervision'])->name('supervision.eliminar')->whereNumber('id');
        });

            // Formatos
        Route::get('/formatos', [App\Http\Controllers\Supervisor\formatoController::class, 'index'])
            ->name('formatos');
        Route::get('/formatos/{id}/download', [App\Http\Controllers\Supervisor\formatoController::class, 'download'])
            ->name('formatos.download');

        // ========================================
        // REPORTES 
        // ========================================
        Route::prefix('reportes')->name('reportes.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Supervisor\SupervisorReporteController::class, 'index'])->name('index');
            Route::get('/export-excel', [\App\Http\Controllers\Supervisor\SupervisorReporteController::class, 'exportExcel'])->name('excel');
            Route::get('/export-pdf', [\App\Http\Controllers\Supervisor\SupervisorReporteController::class, 'exportPdf'])->name('pdf');
        });
        Route::get('/historial', [\App\Http\Controllers\Supervisor\SupervisorReporteController::class, 'historial'])
            ->name('historial');
            Route::get('/reportes/historial', [\App\Http\Controllers\Supervisor\SupervisorReporteController::class, 'historial'])
    ->name('reportes.historial');


        
    });