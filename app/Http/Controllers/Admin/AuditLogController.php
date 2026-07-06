<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin,super_admin');
    }

    public function index(Request $request)
    {
        $query = AuditLog::query();

        // Filters
        if ($request->has('module') && $request->module) {
            $query->where('module', $request->module);
        }
        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
        }
        if ($request->has('date') && $request->date) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->has('json') && $request->json == 1) {
            $logs = $query->orderBy('created_at', 'desc')->paginate(50);
            return response()->json([
                'success' => true,
                'data' => $logs->items(),
                'meta' => [
                    'total' => $logs->total(),
                    'current_page' => $logs->currentPage(),
                    'last_page' => $logs->lastPage(),
                ]
            ]);
        }

        return view('admin.audit-logs');
    }
}