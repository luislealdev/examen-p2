<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AuditController extends Controller
{
    /**
     * Mostrar los logs de auditoría
     */
    public function index(Request $request): View
    {
        $query = DB::table('audit_logs')
            ->leftJoin('users', 'audit_logs.user_id', '=', 'users.id')
            ->select([
                'audit_logs.*',
                'users.name as user_name',
                'users.email as user_email',
                'users.role as user_role'
            ])
            ->orderBy('audit_logs.created_at', 'desc');

        // Filtros
        if ($request->filled('user_id')) {
            $query->where('audit_logs.user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('audit_logs.action', $request->action);
        }

        if ($request->filled('resource')) {
            $query->where('audit_logs.resource', $request->resource);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('audit_logs.created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('audit_logs.created_at', '<=', $request->date_to);
        }

        if ($request->filled('ip_address')) {
            $query->where('audit_logs.ip_address', 'like', '%' . $request->ip_address . '%');
        }

        // Búsqueda por texto
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%")
                  ->orWhere('audit_logs.url', 'like', "%{$search}%")
                  ->orWhere('audit_logs.ip_address', 'like', "%{$search}%");
            });
        }

        $logs = $query->paginate(25)->withQueryString();

        // Obtener datos para los filtros
        $users = DB::table('users')
            ->whereIn('id', function($query) {
                $query->select('user_id')
                      ->from('audit_logs')
                      ->whereNotNull('user_id')
                      ->distinct();
            })
            ->select('id', 'name', 'email', 'role')
            ->orderBy('name')
            ->get();

        $actions = DB::table('audit_logs')
            ->select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        $resources = DB::table('audit_logs')
            ->select('resource')
            ->whereNotNull('resource')
            ->distinct()
            ->orderBy('resource')
            ->pluck('resource');

        return view('audit.index', compact('logs', 'users', 'actions', 'resources'));
    }

    /**
     * Display the specified audit log.
     */
    public function show($id)
    {
        $auditLog = DB::table('audit_logs')
            ->leftJoin('users', 'audit_logs.user_id', '=', 'users.id')
            ->select(
                'audit_logs.*',
                'users.email as user_email',
                'users.role as user_role'
            )
            ->where('audit_logs.id', $id)
            ->first();

        if (!$auditLog) {
            abort(404);
        }

        // Get previous and next logs for navigation
        $previousLog = DB::table('audit_logs')
            ->where('id', '<', $id)
            ->orderBy('id', 'desc')
            ->first();

        $nextLog = DB::table('audit_logs')
            ->where('id', '>', $id)
            ->orderBy('id', 'asc')
            ->first();

        return view('audit.show', compact('auditLog', 'previousLog', 'nextLog'));
    }

    /**
     * Display audit statistics.
     */
    public function statistics()
    {
        // Total logs
        $totalLogs = DB::table('audit_logs')->count();

        // Active users (users with logs in last 30 days)
        $activeUsers = DB::table('audit_logs')
            ->where('created_at', '>=', now()->subDays(30))
            ->distinct('user_id')
            ->count();

        // Today's logs
        $todayLogs = DB::table('audit_logs')
            ->whereDate('created_at', today())
            ->count();

        // Failed requests (4xx, 5xx)
        $failedRequests = DB::table('audit_logs')
            ->where('response_code', '>=', 400)
            ->count();

        // Actions by type
        $actionsByType = DB::table('audit_logs')
            ->select('action', DB::raw('COUNT(*) as count'))
            ->groupBy('action')
            ->orderBy('count', 'desc')
            ->get();

        // Top users
        $topUsers = DB::table('audit_logs')
            ->leftJoin('users', 'audit_logs.user_id', '=', 'users.id')
            ->select(
                'audit_logs.user_id',
                'users.email as user_email',
                'users.role as user_role',
                DB::raw('COUNT(*) as action_count'),
                DB::raw('MAX(audit_logs.created_at) as last_activity')
            )
            ->groupBy('audit_logs.user_id', 'users.email', 'users.role')
            ->orderBy('action_count', 'desc')
            ->limit(10)
            ->get();

        // Daily activity (last 7 days)
        $dailyActivity = DB::table('audit_logs')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        // HTTP methods
        $methods = DB::table('audit_logs')
            ->select('method', DB::raw('COUNT(*) as count'))
            ->groupBy('method')
            ->orderBy('count', 'desc')
            ->get();

        // Response codes
        $responseCodes = DB::table('audit_logs')
            ->select('response_code', DB::raw('COUNT(*) as count'))
            ->groupBy('response_code')
            ->orderBy('count', 'desc')
            ->get();

        $stats = [
            'total_logs' => $totalLogs,
            'active_users' => $activeUsers,
            'today_logs' => $todayLogs,
            'failed_requests' => $failedRequests,
            'actions_by_type' => $actionsByType,
            'top_users' => $topUsers,
            'daily_activity' => $dailyActivity,
            'methods' => $methods,
            'response_codes' => $responseCodes,
        ];

        return view('audit.statistics', compact('stats'));
    }

    /**
     * Limpiar logs antiguos
     */
    public function cleanup(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365'
        ]);

        $days = $request->days;
        $cutoffDate = now()->subDays($days);
        
        $deletedCount = DB::table('audit_logs')
            ->where('created_at', '<', $cutoffDate)
            ->delete();

        return redirect()->route('audit.statistics')
            ->with('success', "Se eliminaron {$deletedCount} registros de auditoría anteriores a {$days} días.");
    }
}
