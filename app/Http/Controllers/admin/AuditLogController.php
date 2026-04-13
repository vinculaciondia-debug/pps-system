<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::latest();

        if ($request->filled('accion')) {
            $query->where('accion', $request->accion);
        }

        if ($request->filled('usuario')) {
            $query->where('user_nombre', 'like', '%' . $request->usuario . '%');
        }

        if ($request->filled('desde')) {
            $query->whereDate('created_at', '>=', $request->desde);
        }

        if ($request->filled('hasta')) {
            $query->whereDate('created_at', '<=', $request->hasta);
        }

        $logs = $query->paginate(30)->withQueryString();

        $acciones = AuditLog::select('accion')->distinct()->orderBy('accion')->pluck('accion');

        return view('admin.audit_logs', compact('logs', 'acciones'));
    }
}
