<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Film;
use App\Models\Store;
use App\Models\Customer;
use App\Models\Rental;
use App\Models\Category;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;

class AdminController extends Controller
{
    /**
     * Show admin dashboard
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_films' => Film::count(),
            'total_stores' => Store::count(),
            'total_customers' => Customer::count(),
            'active_rentals' => Rental::whereNull('return_date')->count(),
            'total_revenue' => Payment::sum('amount'),
            'rentals_today' => Rental::whereDate('rental_date', today())->count(),
            'returns_today' => Rental::whereDate('return_date', today())->count(),
            'recent_users' => User::latest()->take(5)->get(),
            'recent_films' => Film::latest()->take(5)->get(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    /**
     * Show users management
     */
    public function users()
    {
        $users = User::latest()->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show create user form
     */
    public function createUser()
    {
        return view('admin.users.create');
    }

    /**
     * Store new user (employee or admin)
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in([User::ROLE_EMPLOYEE, User::ROLE_ADMIN])],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        $roleText = $request->role === User::ROLE_ADMIN ? 'administrador' : 'empleado';

        return redirect()->route('admin.users')
            ->with('success', "Usuario {$roleText} '{$user->name}' creado exitosamente.");
    }

    /**
     * Show edit user form
     */
    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(User::ROLES)],
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users')
            ->with('success', "Usuario '{$user->name}' actualizado exitosamente.");
    }

    /**
     * Delete user
     */
    public function deleteUser(User $user)
    {
        // Prevent deleting the last admin
        if ($user->isAdmin() && User::where('role', User::ROLE_ADMIN)->count() <= 1) {
            return back()->with('error', 'No puedes eliminar el último administrador del sistema.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users')
            ->with('success', "Usuario '{$name}' eliminado exitosamente.");
    }

    /**
     * Estadísticas de rentas por sucursal
     */
    public function rentalsByStore(Request $request): JsonResponse
    {
        try {
            $startDate = $request->get('start_date', now()->subMonth()->format('Y-m-d'));
            $endDate = $request->get('end_date', now()->format('Y-m-d'));

            $stats = DB::table('stores as store')
                ->select([
                    'store.store_id',
                    'store.manager_staff_id',
                    DB::raw('COALESCE(COUNT(rental.rental_id), 0) as total_rentals'),
                    DB::raw('COALESCE(SUM(payment.amount), 0) as total_revenue'),
                    DB::raw('COALESCE(COUNT(DISTINCT rental.customer_id), 0) as unique_customers'),
                    DB::raw('CASE WHEN COUNT(payment.amount) > 0 THEN AVG(payment.amount) ELSE 0 END as avg_rental_amount')
                ])
                ->leftJoin('inventory', 'store.store_id', '=', 'inventory.store_id')
                ->leftJoin('rental', function($join) use ($startDate, $endDate) {
                    $join->on('inventory.inventory_id', '=', 'rental.inventory_id')
                         ->whereBetween('rental.rental_date', [$startDate, $endDate]);
                })
                ->leftJoin('payment', 'rental.rental_id', '=', 'payment.rental_id')
                ->groupBy('store.store_id', 'store.manager_staff_id')
                ->orderBy('total_rentals', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $stats,
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al cargar estadísticas por sucursal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Estadísticas de rentas por categoría
     */
    public function rentalsByCategory(Request $request): JsonResponse
    {
        try {
            $startDate = $request->get('start_date', now()->subMonth()->format('Y-m-d'));
            $endDate = $request->get('end_date', now()->format('Y-m-d'));

            $stats = DB::table('category')
                ->select([
                    'category.category_id',
                    'category.name',
                    DB::raw('COALESCE(COUNT(rental.rental_id), 0) as total_rentals'),
                    DB::raw('COALESCE(SUM(payment.amount), 0) as total_revenue'),
                    DB::raw('COALESCE(COUNT(DISTINCT film.film_id), 0) as films_rented'),
                    DB::raw('CASE WHEN COUNT(payment.amount) > 0 THEN AVG(payment.amount) ELSE 0 END as avg_rental_amount')
                ])
                ->leftJoin('film', 'category.category_id', '=', 'film.category_id')
                ->leftJoin('inventory', 'film.film_id', '=', 'inventory.film_id')
                ->leftJoin('rental', function($join) use ($startDate, $endDate) {
                    $join->on('inventory.inventory_id', '=', 'rental.inventory_id')
                         ->whereBetween('rental.rental_date', [$startDate, $endDate]);
                })
                ->leftJoin('payment', 'rental.rental_id', '=', 'payment.rental_id')
                ->groupBy('category.category_id', 'category.name')
                ->orderBy('total_rentals', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $stats,
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al cargar estadísticas por categoría: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Estadísticas de rentas por actor
     */
    public function rentalsByActor(Request $request): JsonResponse
    {
        try {
            $startDate = $request->get('start_date', now()->subMonth()->format('Y-m-d'));
            $endDate = $request->get('end_date', now()->format('Y-m-d'));

            $stats = DB::table('film')
                ->select([
                    'film.actors',
                    DB::raw('COALESCE(COUNT(rental.rental_id), 0) as total_rentals'),
                    DB::raw('COALESCE(SUM(payment.amount), 0) as total_revenue'),
                    DB::raw('COALESCE(COUNT(DISTINCT film.film_id), 0) as films_count'),
                    DB::raw('CASE WHEN COUNT(payment.amount) > 0 THEN AVG(payment.amount) ELSE 0 END as avg_rental_amount')
                ])
                ->leftJoin('inventory', 'film.film_id', '=', 'inventory.film_id')
                ->leftJoin('rental', function($join) use ($startDate, $endDate) {
                    $join->on('inventory.inventory_id', '=', 'rental.inventory_id')
                         ->whereBetween('rental.rental_date', [$startDate, $endDate]);
                })
                ->leftJoin('payment', 'rental.rental_id', '=', 'payment.rental_id')
                ->whereNotNull('film.actors')
                ->where('film.actors', '!=', '')
                ->groupBy('film.actors')
                ->orderBy('total_rentals', 'desc')
                ->limit(50)
                ->get();

            // Procesar actores individuales si están separados por comas
            $processedStats = [];
            foreach ($stats as $stat) {
                $actors = explode(',', $stat->actors);
                foreach ($actors as $actor) {
                    $actor = trim($actor);
                    if (empty($actor)) continue;
                    
                    if (!isset($processedStats[$actor])) {
                        $processedStats[$actor] = [
                            'actor' => $actor,
                            'total_rentals' => 0,
                            'total_revenue' => 0,
                            'films_count' => 0,
                            'avg_rental_amount' => 0
                        ];
                    }
                    $processedStats[$actor]['total_rentals'] += (int)$stat->total_rentals;
                    $processedStats[$actor]['total_revenue'] += (float)$stat->total_revenue;
                    $processedStats[$actor]['films_count'] += (int)$stat->films_count;
                }
            }

            // Recalcular promedios y ordenar
            foreach ($processedStats as &$actor) {
                $actor['avg_rental_amount'] = $actor['total_rentals'] > 0 
                    ? $actor['total_revenue'] / $actor['total_rentals'] 
                    : 0;
            }

            uasort($processedStats, function($a, $b) {
                return $b['total_rentals'] <=> $a['total_rentals'];
            });

            return response()->json([
                'success' => true,
                'data' => array_values(array_slice($processedStats, 0, 20)),
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al cargar estadísticas por actor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reporte de ingresos por tienda
     */
    public function revenueByStore(Request $request): JsonResponse
    {
        try {
            $startDate = $request->get('start_date', now()->subMonth()->format('Y-m-d'));
            $endDate = $request->get('end_date', now()->format('Y-m-d'));
            $groupBy = $request->get('group_by', 'day'); // day, week, month

            $dateFormat = match($groupBy) {
                'week' => '%Y-%u',
                'month' => '%Y-%m',
                default => '%Y-%m-%d'
            };

            $revenue = DB::table('stores as store')
                ->select([
                    'store.store_id',
                    DB::raw("DATE_FORMAT(rental.rental_date, '$dateFormat') as period"),
                    DB::raw('COALESCE(SUM(payment.amount), 0) as revenue'),
                    DB::raw('COALESCE(COUNT(rental.rental_id), 0) as rentals_count')
                ])
                ->leftJoin('inventory', 'store.store_id', '=', 'inventory.store_id')
                ->leftJoin('rental', function($join) use ($startDate, $endDate) {
                    $join->on('inventory.inventory_id', '=', 'rental.inventory_id')
                         ->whereBetween('rental.rental_date', [$startDate, $endDate]);
                })
                ->leftJoin('payment', 'rental.rental_id', '=', 'payment.rental_id')
                ->groupBy('store.store_id', DB::raw("DATE_FORMAT(rental.rental_date, '$dateFormat')"))
                ->orderBy('period')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $revenue,
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'group_by' => $groupBy
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al cargar ingresos por tienda: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reporte de ingresos globales
     */
    public function globalRevenue(Request $request): JsonResponse
    {
        try {
            $startDate = $request->get('start_date', now()->subMonth()->format('Y-m-d'));
            $endDate = $request->get('end_date', now()->format('Y-m-d'));
            $groupBy = $request->get('group_by', 'day');

            $dateFormat = match($groupBy) {
                'week' => '%Y-%u',
                'month' => '%Y-%m',
                default => '%Y-%m-%d'
            };

            $revenue = DB::table('payment')
                ->select([
                    DB::raw("DATE_FORMAT(payment.payment_date, '$dateFormat') as period"),
                    DB::raw('COALESCE(SUM(payment.amount), 0) as total_revenue'),
                    DB::raw('COALESCE(COUNT(payment.payment_id), 0) as total_payments'),
                    DB::raw('CASE WHEN COUNT(payment.amount) > 0 THEN AVG(payment.amount) ELSE 0 END as avg_payment'),
                    DB::raw('COALESCE(COUNT(DISTINCT rental.customer_id), 0) as unique_customers')
                ])
                ->leftJoin('rental', 'payment.rental_id', '=', 'rental.rental_id')
                ->whereBetween('payment.payment_date', [$startDate, $endDate])
                ->groupBy(DB::raw("DATE_FORMAT(payment.payment_date, '$dateFormat')"))
                ->orderBy('period')
                ->get();

            $summary = [
                'total_revenue' => $revenue->sum('total_revenue'),
                'total_payments' => $revenue->sum('total_payments'),
                'avg_daily_revenue' => $revenue->count() > 0 ? $revenue->avg('total_revenue') : 0,
                'unique_customers' => $revenue->max('unique_customers') ?: 0
            ];

            return response()->json([
                'success' => true,
                'data' => $revenue,
                'summary' => $summary,
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'group_by' => $groupBy
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al cargar ingresos globales: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clientes con mayor número de rentas
     */
    public function topCustomers(Request $request): JsonResponse
    {
        try {
            $limit = $request->get('limit', 20);
            $startDate = $request->get('start_date', now()->subYear()->format('Y-m-d'));
            $endDate = $request->get('end_date', now()->format('Y-m-d'));

            $customers = DB::table('customers as customer')
                ->select([
                    'customer.customer_id',
                    'customer.first_name',
                    'customer.last_name',
                    'customer.email',
                    'customer.create_date',
                    DB::raw('COALESCE(COUNT(rental.rental_id), 0) as total_rentals'),
                    DB::raw('COALESCE(SUM(payment.amount), 0) as total_spent'),
                    DB::raw('CASE WHEN COUNT(payment.amount) > 0 THEN AVG(payment.amount) ELSE 0 END as avg_rental_cost'),
                    DB::raw('MAX(rental.rental_date) as last_rental_date'),
                    DB::raw('COALESCE(COUNT(DISTINCT film.category_id), 0) as categories_rented')
                ])
                ->leftJoin('rental', function($join) use ($startDate, $endDate) {
                    $join->on('customer.customer_id', '=', 'rental.customer_id')
                         ->whereBetween('rental.rental_date', [$startDate, $endDate]);
                })
                ->leftJoin('payment', 'rental.rental_id', '=', 'payment.rental_id')
                ->leftJoin('inventory', 'rental.inventory_id', '=', 'inventory.inventory_id')
                ->leftJoin('film', 'inventory.film_id', '=', 'film.film_id')
                ->groupBy([
                    'customer.customer_id',
                    'customer.first_name',
                    'customer.last_name',
                    'customer.email',
                    'customer.create_date'
                ])
                ->orderBy('total_rentals', 'desc')
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $customers,
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'limit' => $limit
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al cargar top clientes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exportar reportes en CSV
     */
    public function exportCSV(Request $request)
    {
        try {
            $type = $request->get('type'); // 'stores', 'categories', 'customers', 'revenue'
            $startDate = $request->get('start_date', now()->subMonth()->format('Y-m-d'));
            $endDate = $request->get('end_date', now()->format('Y-m-d'));
            
            $filename = "reporte_{$type}_" . now()->format('Y-m-d_H-i-s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0'
            ];

            $callback = function() use ($type, $startDate, $endDate) {
                $file = fopen('php://output', 'w');
                
                switch ($type) {
                    case 'stores':
                        $this->exportStoresCSV($file, $startDate, $endDate);
                        break;
                    case 'categories':
                        $this->exportCategoriesCSV($file, $startDate, $endDate);
                        break;
                    case 'customers':
                        $this->exportCustomersCSV($file, $startDate, $endDate);
                        break;
                    case 'revenue':
                        $this->exportRevenueCSV($file, $startDate, $endDate);
                        break;
                    default:
                        fputcsv($file, ['Error: Tipo de reporte no válido']);
                }
                
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al exportar CSV: ' . $e->getMessage());
        }
    }

    /**
     * Helper methods for CSV export
     */
    private function exportStoresCSV($file, $startDate, $endDate)
    {
        fputcsv($file, ['Store ID', 'Total Rentals', 'Total Revenue', 'Unique Customers', 'Avg Rental Amount']);
        
        $stats = DB::table('stores as store')
            ->select([
                'store.store_id',
                DB::raw('COALESCE(COUNT(rental.rental_id), 0) as total_rentals'),
                DB::raw('COALESCE(SUM(payment.amount), 0) as total_revenue'),
                DB::raw('COALESCE(COUNT(DISTINCT rental.customer_id), 0) as unique_customers'),
                DB::raw('CASE WHEN COUNT(payment.amount) > 0 THEN AVG(payment.amount) ELSE 0 END as avg_rental_amount')
            ])
            ->leftJoin('inventory', 'store.store_id', '=', 'inventory.store_id')
            ->leftJoin('rental', function($join) use ($startDate, $endDate) {
                $join->on('inventory.inventory_id', '=', 'rental.inventory_id')
                     ->whereBetween('rental.rental_date', [$startDate, $endDate]);
            })
            ->leftJoin('payment', 'rental.rental_id', '=', 'payment.rental_id')
            ->groupBy('store.store_id')
            ->get();

        foreach ($stats as $stat) {
            fputcsv($file, [
                $stat->store_id,
                $stat->total_rentals,
                number_format($stat->total_revenue, 2),
                $stat->unique_customers,
                number_format($stat->avg_rental_amount, 2)
            ]);
        }
    }

    private function exportCategoriesCSV($file, $startDate, $endDate)
    {
        fputcsv($file, ['Category', 'Total Rentals', 'Total Revenue', 'Films Rented', 'Avg Rental Amount']);
        
        $stats = DB::table('category')
            ->select([
                'category.name',
                DB::raw('COALESCE(COUNT(rental.rental_id), 0) as total_rentals'),
                DB::raw('COALESCE(SUM(payment.amount), 0) as total_revenue'),
                DB::raw('COALESCE(COUNT(DISTINCT film.film_id), 0) as films_rented'),
                DB::raw('CASE WHEN COUNT(payment.amount) > 0 THEN AVG(payment.amount) ELSE 0 END as avg_rental_amount')
            ])
            ->leftJoin('film', 'category.category_id', '=', 'film.category_id')
            ->leftJoin('inventory', 'film.film_id', '=', 'inventory.film_id')
            ->leftJoin('rental', function($join) use ($startDate, $endDate) {
                $join->on('inventory.inventory_id', '=', 'rental.inventory_id')
                     ->whereBetween('rental.rental_date', [$startDate, $endDate]);
            })
            ->leftJoin('payment', 'rental.rental_id', '=', 'payment.rental_id')
            ->groupBy('category.name')
            ->get();

        foreach ($stats as $stat) {
            fputcsv($file, [
                $stat->name,
                $stat->total_rentals,
                number_format($stat->total_revenue, 2),
                $stat->films_rented,
                number_format($stat->avg_rental_amount, 2)
            ]);
        }
    }

    private function exportCustomersCSV($file, $startDate, $endDate)
    {
        fputcsv($file, ['Customer Name', 'Email', 'Total Rentals', 'Total Spent', 'Avg Rental Cost', 'Last Rental']);
        
        $customers = DB::table('customers as customer')
            ->select([
                'customer.first_name',
                'customer.last_name',
                'customer.email',
                DB::raw('COALESCE(COUNT(rental.rental_id), 0) as total_rentals'),
                DB::raw('COALESCE(SUM(payment.amount), 0) as total_spent'),
                DB::raw('CASE WHEN COUNT(payment.amount) > 0 THEN AVG(payment.amount) ELSE 0 END as avg_rental_cost'),
                DB::raw('MAX(rental.rental_date) as last_rental_date')
            ])
            ->leftJoin('rental', function($join) use ($startDate, $endDate) {
                $join->on('customer.customer_id', '=', 'rental.customer_id')
                     ->whereBetween('rental.rental_date', [$startDate, $endDate]);
            })
            ->leftJoin('payment', 'rental.rental_id', '=', 'payment.rental_id')
            ->groupBy(['customer.customer_id', 'customer.first_name', 'customer.last_name', 'customer.email'])
            ->orderBy('total_rentals', 'desc')
            ->get();

        foreach ($customers as $customer) {
            fputcsv($file, [
                $customer->first_name . ' ' . $customer->last_name,
                $customer->email,
                $customer->total_rentals,
                number_format($customer->total_spent, 2),
                number_format($customer->avg_rental_cost, 2),
                $customer->last_rental_date ? date('Y-m-d', strtotime($customer->last_rental_date)) : 'N/A'
            ]);
        }
    }

    private function exportRevenueCSV($file, $startDate, $endDate)
    {
        fputcsv($file, ['Date', 'Total Revenue', 'Total Payments', 'Avg Payment', 'Unique Customers']);
        
        $revenue = DB::table('payment')
            ->select([
                DB::raw('DATE(payment.payment_date) as date'),
                DB::raw('COALESCE(SUM(payment.amount), 0) as total_revenue'),
                DB::raw('COALESCE(COUNT(payment.payment_id), 0) as total_payments'),
                DB::raw('CASE WHEN COUNT(payment.amount) > 0 THEN AVG(payment.amount) ELSE 0 END as avg_payment'),
                DB::raw('COALESCE(COUNT(DISTINCT rental.customer_id), 0) as unique_customers')
            ])
            ->leftJoin('rental', 'payment.rental_id', '=', 'rental.rental_id')
            ->whereBetween('payment.payment_date', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(payment.payment_date)'))
            ->orderBy('date')
            ->get();

        foreach ($revenue as $row) {
            fputcsv($file, [
                $row->date,
                number_format($row->total_revenue, 2),
                $row->total_payments,
                number_format($row->avg_payment, 2),
                $row->unique_customers
            ]);
        }
    }

    /**
     * Métodos auxiliares para exportación CSV
     */
    private function getStoreStatsForExport($startDate, $endDate)
    {
        return Store::select([
                'store.store_id',
                DB::raw('COUNT(rental.rental_id) as total_rentals'),
                DB::raw('SUM(payment.amount) as total_revenue'),
                DB::raw('COUNT(DISTINCT rental.customer_id) as unique_customers'),
                DB::raw('AVG(payment.amount) as avg_rental_amount')
            ])
            ->leftJoin('inventory', 'store.store_id', '=', 'inventory.store_id')
            ->leftJoin('rental', 'inventory.inventory_id', '=', 'rental.inventory_id')
            ->leftJoin('payment', 'rental.rental_id', '=', 'payment.rental_id')
            ->whereBetween('rental.rental_date', [$startDate, $endDate])
            ->groupBy('store.store_id')
            ->get()
            ->map(function($store) {
                return [
                    $store->store_id,
                    $store->total_rentals,
                    number_format($store->total_revenue, 2),
                    $store->unique_customers,
                    number_format($store->avg_rental_amount, 2)
                ];
            })->toArray();
    }

    private function getCategoryStatsForExport($startDate, $endDate)
    {
        return Category::select([
                'category.name',
                DB::raw('COUNT(rental.rental_id) as total_rentals'),
                DB::raw('SUM(payment.amount) as total_revenue'),
                DB::raw('COUNT(DISTINCT film.film_id) as films_rented'),
                DB::raw('AVG(payment.amount) as avg_rental_amount')
            ])
            ->leftJoin('film', 'category.category_id', '=', 'film.category_id')
            ->leftJoin('inventory', 'film.film_id', '=', 'inventory.film_id')
            ->leftJoin('rental', 'inventory.inventory_id', '=', 'rental.inventory_id')
            ->leftJoin('payment', 'rental.rental_id', '=', 'payment.rental_id')
            ->whereBetween('rental.rental_date', [$startDate, $endDate])
            ->groupBy('category.name')
            ->get()
            ->map(function($category) {
                return [
                    $category->name,
                    $category->total_rentals,
                    number_format($category->total_revenue, 2),
                    $category->films_rented,
                    number_format($category->avg_rental_amount, 2)
                ];
            })->toArray();
    }

    private function getCustomerStatsForExport($startDate, $endDate)
    {
        return Customer::select([
                DB::raw("CONCAT(customer.first_name, ' ', customer.last_name) as full_name"),
                'customer.email',
                DB::raw('COUNT(rental.rental_id) as total_rentals'),
                DB::raw('SUM(payment.amount) as total_spent'),
                DB::raw('AVG(payment.amount) as avg_rental_cost'),
                DB::raw('MAX(rental.rental_date) as last_rental_date')
            ])
            ->leftJoin('rental', 'customer.customer_id', '=', 'rental.customer_id')
            ->leftJoin('payment', 'rental.rental_id', '=', 'payment.rental_id')
            ->whereBetween('rental.rental_date', [$startDate, $endDate])
            ->groupBy(['customer.customer_id', 'customer.first_name', 'customer.last_name', 'customer.email'])
            ->orderBy('total_rentals', 'desc')
            ->limit(100)
            ->get()
            ->map(function($customer) {
                return [
                    $customer->full_name,
                    $customer->email,
                    $customer->total_rentals,
                    number_format($customer->total_spent, 2),
                    number_format($customer->avg_rental_cost, 2),
                    $customer->last_rental_date
                ];
            })->toArray();
    }

    private function getRevenueStatsForExport($startDate, $endDate)
    {
        return DB::table('payment')
            ->select([
                DB::raw("DATE_FORMAT(payment.payment_date, '%Y-%m-%d') as date"),
                DB::raw('SUM(payment.amount) as total_revenue'),
                DB::raw('COUNT(payment.payment_id) as total_payments'),
                DB::raw('AVG(payment.amount) as avg_payment'),
                DB::raw('COUNT(DISTINCT rental.customer_id) as unique_customers')
            ])
            ->leftJoin('rental', 'payment.rental_id', '=', 'rental.rental_id')
            ->whereBetween('payment.payment_date', [$startDate, $endDate])
            ->groupBy(DB::raw("DATE_FORMAT(payment.payment_date, '%Y-%m-%d')"))
            ->orderBy('date')
            ->get()
            ->map(function($day) {
                return [
                    $day->date,
                    number_format($day->total_revenue, 2),
                    $day->total_payments,
                    number_format($day->avg_payment, 2),
                    $day->unique_customers
                ];
            })->toArray();
    }

    /**
     * Exportar reporte en formato PDF
     */
    public function exportPDF(Request $request)
    {
        try {
            $type = $request->get('type');
            $startDate = $request->get('start_date', now()->subMonth()->format('Y-m-d'));
            $endDate = $request->get('end_date', now()->format('Y-m-d'));
            
            $data = [];
            $title = '';
            
            switch ($type) {
                case 'stores':
                    $data = $this->getStoreStatsForPDF($startDate, $endDate);
                    $title = 'Reporte de Rentas por Sucursal';
                    break;
                case 'categories':
                    $data = $this->getCategoryStatsForPDF($startDate, $endDate);
                    $title = 'Reporte de Rentas por Categoría';
                    break;
                case 'customers':
                    $data = $this->getCustomerStatsForPDF($startDate, $endDate);
                    $title = 'Reporte de Top Clientes';
                    break;
                case 'revenue':
                    $data = $this->getRevenueStatsForPDF($startDate, $endDate);
                    $title = 'Reporte de Ingresos';
                    break;
                default:
                    return redirect()->back()->with('error', 'Tipo de reporte no válido');
            }

            $html = $this->generatePDFHTML($title, $data, $type, $startDate, $endDate);
            
            $options = new Options();
            $options->set('defaultFont', 'Arial');
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $filename = "reporte_{$type}_" . now()->format('Y-m-d_H-i-s') . '.pdf';
            
            return response($dompdf->output(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', "attachment; filename=\"$filename\"");
                
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al exportar PDF: ' . $e->getMessage());
        }
    }

    /**
     * Métodos auxiliares para exportación PDF
     */
    private function getStoreStatsForPDF($startDate, $endDate)
    {
        return DB::table('stores as store')
            ->select([
                'store.store_id',
                DB::raw('COALESCE(COUNT(rental.rental_id), 0) as total_rentals'),
                DB::raw('COALESCE(SUM(payment.amount), 0) as total_revenue'),
                DB::raw('COALESCE(COUNT(DISTINCT rental.customer_id), 0) as unique_customers'),
                DB::raw('CASE WHEN COUNT(payment.amount) > 0 THEN AVG(payment.amount) ELSE 0 END as avg_rental_amount')
            ])
            ->leftJoin('inventory', 'store.store_id', '=', 'inventory.store_id')
            ->leftJoin('rental', function($join) use ($startDate, $endDate) {
                $join->on('inventory.inventory_id', '=', 'rental.inventory_id')
                     ->whereBetween('rental.rental_date', [$startDate, $endDate]);
            })
            ->leftJoin('payment', 'rental.rental_id', '=', 'payment.rental_id')
            ->groupBy('store.store_id')
            ->orderBy('total_rentals', 'desc')
            ->get();
    }

    private function getCategoryStatsForPDF($startDate, $endDate)
    {
        return DB::table('category')
            ->select([
                'category.name',
                DB::raw('COALESCE(COUNT(rental.rental_id), 0) as total_rentals'),
                DB::raw('COALESCE(SUM(payment.amount), 0) as total_revenue'),
                DB::raw('COALESCE(COUNT(DISTINCT film.film_id), 0) as films_rented'),
                DB::raw('CASE WHEN COUNT(payment.amount) > 0 THEN AVG(payment.amount) ELSE 0 END as avg_rental_amount')
            ])
            ->leftJoin('film', 'category.category_id', '=', 'film.category_id')
            ->leftJoin('inventory', 'film.film_id', '=', 'inventory.film_id')
            ->leftJoin('rental', function($join) use ($startDate, $endDate) {
                $join->on('inventory.inventory_id', '=', 'rental.inventory_id')
                     ->whereBetween('rental.rental_date', [$startDate, $endDate]);
            })
            ->leftJoin('payment', 'rental.rental_id', '=', 'payment.rental_id')
            ->groupBy('category.name')
            ->orderBy('total_rentals', 'desc')
            ->get();
    }

    private function getCustomerStatsForPDF($startDate, $endDate)
    {
        return DB::table('customers as customer')
            ->select([
                'customer.first_name',
                'customer.last_name',
                'customer.email',
                DB::raw('COALESCE(COUNT(rental.rental_id), 0) as total_rentals'),
                DB::raw('COALESCE(SUM(payment.amount), 0) as total_spent'),
                DB::raw('CASE WHEN COUNT(payment.amount) > 0 THEN AVG(payment.amount) ELSE 0 END as avg_rental_cost'),
                DB::raw('MAX(rental.rental_date) as last_rental_date')
            ])
            ->leftJoin('rental', function($join) use ($startDate, $endDate) {
                $join->on('customer.customer_id', '=', 'rental.customer_id')
                     ->whereBetween('rental.rental_date', [$startDate, $endDate]);
            })
            ->leftJoin('payment', 'rental.rental_id', '=', 'payment.rental_id')
            ->groupBy(['customer.customer_id', 'customer.first_name', 'customer.last_name', 'customer.email'])
            ->orderBy('total_rentals', 'desc')
            ->limit(20)
            ->get();
    }

    private function getRevenueStatsForPDF($startDate, $endDate)
    {
        return DB::table('payment')
            ->select([
                DB::raw('DATE(payment.payment_date) as date'),
                DB::raw('COALESCE(SUM(payment.amount), 0) as total_revenue'),
                DB::raw('COALESCE(COUNT(payment.payment_id), 0) as total_payments'),
                DB::raw('CASE WHEN COUNT(payment.amount) > 0 THEN AVG(payment.amount) ELSE 0 END as avg_payment')
            ])
            ->whereBetween('payment.payment_date', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(payment.payment_date)'))
            ->orderBy('date')
            ->get();
    }

    private function generatePDFHTML($title, $data, $type, $startDate, $endDate)
    {
        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <title>{$title}</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; }
                .header { text-align: center; margin-bottom: 30px; }
                .title { font-size: 18px; font-weight: bold; margin-bottom: 10px; }
                .period { font-size: 14px; color: #666; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f4f4f4; font-weight: bold; }
                .number { text-align: right; }
                .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #666; }
            </style>
        </head>
        <body>
            <div class='header'>
                <div class='title'>{$title}</div>
                <div class='period'>Período: {$startDate} - {$endDate}</div>
            </div>
            
            <table>";

        // Headers y datos según el tipo de reporte
        switch ($type) {
            case 'stores':
                $html .= "
                    <tr>
                        <th>Sucursal</th>
                        <th>Dirección</th>
                        <th>Total Rentas</th>
                        <th>Ingresos</th>
                        <th>Clientes Únicos</th>
                        <th>Promedio por Renta</th>
                    </tr>";
                foreach ($data as $store) {
                    $html .= "
                    <tr>
                        <td>Sucursal {$store->store_id}</td>
                        <td>{$store->address}, {$store->city}</td>
                        <td class='number'>{$store->total_rentals}</td>
                        <td class='number'>$" . number_format($store->total_revenue, 2) . "</td>
                        <td class='number'>{$store->unique_customers}</td>
                        <td class='number'>$" . number_format($store->avg_rental_amount, 2) . "</td>
                    </tr>";
                }
                break;

            case 'categories':
                $html .= "
                    <tr>
                        <th>Categoría</th>
                        <th>Total Rentas</th>
                        <th>Ingresos</th>
                        <th>Películas Rentadas</th>
                        <th>Promedio por Renta</th>
                    </tr>";
                foreach ($data as $category) {
                    $html .= "
                    <tr>
                        <td>{$category->name}</td>
                        <td class='number'>{$category->total_rentals}</td>
                        <td class='number'>$" . number_format($category->total_revenue, 2) . "</td>
                        <td class='number'>{$category->films_rented}</td>
                        <td class='number'>$" . number_format($category->avg_rental_amount, 2) . "</td>
                    </tr>";
                }
                break;

            case 'customers':
                $html .= "
                    <tr>
                        <th>Cliente</th>
                        <th>Email</th>
                        <th>Total Rentas</th>
                        <th>Total Gastado</th>
                        <th>Promedio por Renta</th>
                        <th>Última Renta</th>
                    </tr>";
                foreach ($data as $customer) {
                    $html .= "
                    <tr>
                        <td>{$customer->first_name} {$customer->last_name}</td>
                        <td>{$customer->email}</td>
                        <td class='number'>{$customer->total_rentals}</td>
                        <td class='number'>$" . number_format($customer->total_spent, 2) . "</td>
                        <td class='number'>$" . number_format($customer->avg_rental_cost, 2) . "</td>
                        <td>{$customer->last_rental_date}</td>
                    </tr>";
                }
                break;

            case 'revenue':
                $html .= "
                    <tr>
                        <th>Fecha</th>
                        <th>Ingresos</th>
                        <th>Total Pagos</th>
                        <th>Promedio por Pago</th>
                    </tr>";
                foreach ($data as $day) {
                    $html .= "
                    <tr>
                        <td>{$day->date}</td>
                        <td class='number'>$" . number_format($day->total_revenue, 2) . "</td>
                        <td class='number'>{$day->total_payments}</td>
                        <td class='number'>$" . number_format($day->avg_payment, 2) . "</td>
                    </tr>";
                }
                break;
        }

        $html .= "
            </table>
            
            <div class='footer'>
                Generado el " . now()->format('d/m/Y H:i:s') . " | Sistema de Gestión de Videoclub
            </div>
        </body>
        </html>";

        return $html;
    }
}
