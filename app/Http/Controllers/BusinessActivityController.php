<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BusinessActivityController extends Controller
{
    /**
     * Display business activity logs with advanced filtering
     */
    public function index(Request $request): View
    {
        $query = DB::table('business_activity_logs')
            ->leftJoin('users', 'business_activity_logs.user_id', '=', 'users.id')
            ->leftJoin('customers', 'business_activity_logs.customer_id', '=', 'customers.customer_id')
            ->leftJoin('film', 'business_activity_logs.film_id', '=', 'film.film_id')
            ->leftJoin('staff', 'business_activity_logs.staff_id', '=', 'staff.staff_id')
            ->leftJoin('stores', 'business_activity_logs.store_id', '=', 'stores.store_id')
            ->select([
                'business_activity_logs.*',
                'users.name as user_name',
                'users.email as user_email',
                DB::raw('CONCAT(customers.first_name, " ", customers.last_name) as customer_name'),
                'film.title as film_title',
                DB::raw('CONCAT(staff.first_name, " ", staff.last_name) as staff_name'),
                'stores.store_id as store_number'
            ]);

        // Filtros
        if ($request->filled('category')) {
            $query->where('business_activity_logs.category', $request->category);
        }

        if ($request->filled('action')) {
            $query->where('business_activity_logs.action', $request->action);
        }

        if ($request->filled('severity')) {
            $query->where('business_activity_logs.severity', $request->severity);
        }

        if ($request->filled('user_id')) {
            $query->where('business_activity_logs.user_id', $request->user_id);
        }

        if ($request->filled('customer_id')) {
            $query->where('business_activity_logs.customer_id', $request->customer_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('business_activity_logs.created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('business_activity_logs.created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('users.name', 'LIKE', "%{$search}%")
                  ->orWhere('users.email', 'LIKE', "%{$search}%")
                  ->orWhere('customers.first_name', 'LIKE', "%{$search}%")
                  ->orWhere('customers.last_name', 'LIKE', "%{$search}%")
                  ->orWhere('film.title', 'LIKE', "%{$search}%")
                  ->orWhere('business_activity_logs.details', 'LIKE', "%{$search}%");
            });
        }

        // Ordenamiento
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        $validSortFields = ['created_at', 'category', 'action', 'severity', 'user_name'];
        if (!in_array($sortField, $validSortFields)) {
            $sortField = 'created_at';
        }

        if ($sortField === 'user_name') {
            $query->orderBy('users.name', $sortDirection);
        } else {
            $query->orderBy('business_activity_logs.' . $sortField, $sortDirection);
        }

        $logs = $query->paginate(20)->withQueryString();

        // Datos para filtros
        $categories = DB::table('business_activity_logs')
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $actions = DB::table('business_activity_logs')
            ->select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        $users = DB::table('users')
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        return view('admin.business-activity.index', compact(
            'logs', 
            'categories', 
            'actions', 
            'users'
        ));
    }

    /**
     * Show detailed view of a specific log entry
     */
    public function show($id): View
    {
        $log = DB::table('business_activity_logs')
            ->leftJoin('users', 'business_activity_logs.user_id', '=', 'users.id')
            ->leftJoin('customers', 'business_activity_logs.customer_id', '=', 'customers.customer_id')
            ->leftJoin('film', 'business_activity_logs.film_id', '=', 'film.film_id')
            ->leftJoin('staff', 'business_activity_logs.staff_id', '=', 'staff.staff_id')
            ->leftJoin('stores', 'business_activity_logs.store_id', '=', 'stores.store_id')
            ->select([
                'business_activity_logs.*',
                'users.name as user_name',
                'users.email as user_email',
                'users.role as user_role',
                DB::raw('CONCAT(customers.first_name, " ", customers.last_name) as customer_name'),
                'customers.email as customer_email',
                'film.title as film_title',
                'film.description as film_description',
                DB::raw('CONCAT(staff.first_name, " ", staff.last_name) as staff_name'),
                'staff.email as staff_email',
                'stores.store_id as store_number'
            ])
            ->where('business_activity_logs.id', $id)
            ->first();

        if (!$log) {
            abort(404, 'Log de actividad no encontrado');
        }

        // Decodificar detalles JSON
        $log->details = json_decode($log->details, true);

        return view('admin.business-activity.show', compact('log'));
    }

    /**
     * Dashboard with activity statistics
     */
    public function dashboard(): View
    {
        // Actividad de hoy
        $todayLogs = DB::table('business_activity_logs')
            ->whereDate('created_at', today())
            ->count();

        // Actividad por categoría (últimos 30 días)
        $categoryStats = DB::table('business_activity_logs')
            ->select('category', DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('category')
            ->orderBy('count', 'desc')
            ->get();

        // Actividad por severidad (últimos 7 días)
        $severityStats = DB::table('business_activity_logs')
            ->select('severity', DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('severity')
            ->get();

        // Usuarios más activos (últimos 30 días)
        $activeUsers = DB::table('business_activity_logs')
            ->join('users', 'business_activity_logs.user_id', '=', 'users.id')
            ->select(
                'users.name',
                'users.email',
                DB::raw('COUNT(*) as activity_count'),
                DB::raw('MAX(business_activity_logs.created_at) as last_activity')
            )
            ->where('business_activity_logs.created_at', '>=', now()->subDays(30))
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderBy('activity_count', 'desc')
            ->limit(10)
            ->get();

        // Actividad diaria (últimos 14 días)
        $dailyActivity = DB::table('business_activity_logs')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(14))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'asc')
            ->get();

        // Actividades críticas recientes
        $criticalActivities = DB::table('business_activity_logs')
            ->leftJoin('users', 'business_activity_logs.user_id', '=', 'users.id')
            ->select([
                'business_activity_logs.*',
                'users.name as user_name'
            ])
            ->whereIn('severity', ['high', 'critical'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.business-activity.dashboard', compact(
            'todayLogs',
            'categoryStats',
            'severityStats',
            'activeUsers',
            'dailyActivity',
            'criticalActivities'
        ));
    }
}
