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
        $startDate = $request->get('start_date', now()->subMonth());
        $endDate = $request->get('end_date', now());

        $stats = Store::select([
                'store.store_id',
                'store.manager_staff_id',
                DB::raw('COUNT(rental.rental_id) as total_rentals'),
                DB::raw('SUM(payment.amount) as total_revenue'),
                DB::raw('COUNT(DISTINCT rental.customer_id) as unique_customers'),
                DB::raw('AVG(payment.amount) as avg_rental_amount')
            ])
            ->leftJoin('inventory', 'store.store_id', '=', 'inventory.store_id')
            ->leftJoin('rental', 'inventory.inventory_id', '=', 'rental.inventory_id')
            ->leftJoin('payment', 'rental.rental_id', '=', 'payment.rental_id')
            ->whereBetween('rental.rental_date', [$startDate, $endDate])
            ->groupBy('store.store_id', 'store.manager_staff_id')
            ->orderBy('total_rentals', 'desc')
            ->get();

        return response()->json([
            'data' => $stats,
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ]
        ]);
    }

    /**
     * Estadísticas de rentas por categoría
     */
    public function rentalsByCategory(Request $request): JsonResponse
    {
        $startDate = $request->get('start_date', now()->subMonth());
        $endDate = $request->get('end_date', now());

        $stats = Category::select([
                'category.category_id',
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
            ->groupBy('category.category_id', 'category.name')
            ->orderBy('total_rentals', 'desc')
            ->get();

        return response()->json([
            'data' => $stats,
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ]
        ]);
    }

    /**
     * Estadísticas de rentas por actor
     */
    public function rentalsByActor(Request $request): JsonResponse
    {
        $startDate = $request->get('start_date', now()->subMonth());
        $endDate = $request->get('end_date', now());

        $stats = DB::table('film')
            ->select([
                'film.actors',
                DB::raw('COUNT(rental.rental_id) as total_rentals'),
                DB::raw('SUM(payment.amount) as total_revenue'),
                DB::raw('COUNT(DISTINCT film.film_id) as films_count'),
                DB::raw('AVG(payment.amount) as avg_rental_amount')
            ])
            ->leftJoin('inventory', 'film.film_id', '=', 'inventory.film_id')
            ->leftJoin('rental', 'inventory.inventory_id', '=', 'rental.inventory_id')
            ->leftJoin('payment', 'rental.rental_id', '=', 'payment.rental_id')
            ->whereNotNull('film.actors')
            ->whereBetween('rental.rental_date', [$startDate, $endDate])
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
                if (!isset($processedStats[$actor])) {
                    $processedStats[$actor] = [
                        'actor' => $actor,
                        'total_rentals' => 0,
                        'total_revenue' => 0,
                        'films_count' => 0,
                        'avg_rental_amount' => 0
                    ];
                }
                $processedStats[$actor]['total_rentals'] += $stat->total_rentals;
                $processedStats[$actor]['total_revenue'] += $stat->total_revenue;
                $processedStats[$actor]['films_count'] += $stat->films_count;
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
            'data' => array_values(array_slice($processedStats, 0, 20)),
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ]
        ]);
    }

    /**
     * Reporte de ingresos por tienda
     */
    public function revenueByStore(Request $request): JsonResponse
    {
        $startDate = $request->get('start_date', now()->subMonth());
        $endDate = $request->get('end_date', now());
        $groupBy = $request->get('group_by', 'day'); // day, week, month

        $dateFormat = match($groupBy) {
            'week' => '%Y-%u',
            'month' => '%Y-%m',
            default => '%Y-%m-%d'
        };

        $revenue = Store::select([
                'store.store_id',
                DB::raw("DATE_FORMAT(rental.rental_date, '$dateFormat') as period"),
                DB::raw('SUM(payment.amount) as revenue'),
                DB::raw('COUNT(rental.rental_id) as rentals_count')
            ])
            ->leftJoin('inventory', 'store.store_id', '=', 'inventory.store_id')
            ->leftJoin('rental', 'inventory.inventory_id', '=', 'rental.inventory_id')
            ->leftJoin('payment', 'rental.rental_id', '=', 'payment.rental_id')
            ->whereBetween('rental.rental_date', [$startDate, $endDate])
            ->groupBy('store.store_id', DB::raw("DATE_FORMAT(rental.rental_date, '$dateFormat')"))
            ->orderBy('period')
            ->get();

        return response()->json([
            'data' => $revenue,
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'group_by' => $groupBy
            ]
        ]);
    }

    /**
     * Reporte de ingresos globales
     */
    public function globalRevenue(Request $request): JsonResponse
    {
        $startDate = $request->get('start_date', now()->subMonth());
        $endDate = $request->get('end_date', now());
        $groupBy = $request->get('group_by', 'day');

        $dateFormat = match($groupBy) {
            'week' => '%Y-%u',
            'month' => '%Y-%m',
            default => '%Y-%m-%d'
        };

        $revenue = DB::table('payment')
            ->select([
                DB::raw("DATE_FORMAT(payment.payment_date, '$dateFormat') as period"),
                DB::raw('SUM(payment.amount) as total_revenue'),
                DB::raw('COUNT(payment.payment_id) as total_payments'),
                DB::raw('AVG(payment.amount) as avg_payment'),
                DB::raw('COUNT(DISTINCT rental.customer_id) as unique_customers')
            ])
            ->leftJoin('rental', 'payment.rental_id', '=', 'rental.rental_id')
            ->whereBetween('payment.payment_date', [$startDate, $endDate])
            ->groupBy(DB::raw("DATE_FORMAT(payment.payment_date, '$dateFormat')"))
            ->orderBy('period')
            ->get();

        $summary = [
            'total_revenue' => $revenue->sum('total_revenue'),
            'total_payments' => $revenue->sum('total_payments'),
            'avg_daily_revenue' => $revenue->avg('total_revenue'),
            'unique_customers' => $revenue->max('unique_customers')
        ];

        return response()->json([
            'data' => $revenue,
            'summary' => $summary,
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'group_by' => $groupBy
            ]
        ]);
    }

    /**
     * Clientes con mayor número de rentas
     */
    public function topCustomers(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 20);
        $startDate = $request->get('start_date', now()->subYear());
        $endDate = $request->get('end_date', now());

        $customers = Customer::select([
                'customer.customer_id',
                'customer.first_name',
                'customer.last_name',
                'customer.email',
                'customer.create_date',
                DB::raw('COUNT(rental.rental_id) as total_rentals'),
                DB::raw('SUM(payment.amount) as total_spent'),
                DB::raw('AVG(payment.amount) as avg_rental_cost'),
                DB::raw('MAX(rental.rental_date) as last_rental_date'),
                DB::raw('COUNT(DISTINCT film.category_id) as categories_rented')
            ])
            ->leftJoin('rental', 'customer.customer_id', '=', 'rental.customer_id')
            ->leftJoin('payment', 'rental.rental_id', '=', 'payment.rental_id')
            ->leftJoin('inventory', 'rental.inventory_id', '=', 'inventory.inventory_id')
            ->leftJoin('film', 'inventory.film_id', '=', 'film.film_id')
            ->whereBetween('rental.rental_date', [$startDate, $endDate])
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
            'data' => $customers,
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'limit' => $limit
            ]
        ]);
    }

    /**
     * Exportar reporte en formato CSV
     */
    public function exportCSV(Request $request)
    {
        $type = $request->get('type'); // 'stores', 'categories', 'customers', 'revenue'
        $startDate = $request->get('start_date', now()->subMonth());
        $endDate = $request->get('end_date', now());
        
        $filename = "reporte_{$type}_" . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($type, $startDate, $endDate) {
            $file = fopen('php://output', 'w');
            
            switch ($type) {
                case 'stores':
                    fputcsv($file, ['Store ID', 'Total Rentals', 'Total Revenue', 'Unique Customers', 'Avg Rental Amount']);
                    $data = $this->getStoreStatsForExport($startDate, $endDate);
                    break;
                case 'categories':
                    fputcsv($file, ['Category', 'Total Rentals', 'Total Revenue', 'Films Rented', 'Avg Rental Amount']);
                    $data = $this->getCategoryStatsForExport($startDate, $endDate);
                    break;
                case 'customers':
                    fputcsv($file, ['Customer Name', 'Email', 'Total Rentals', 'Total Spent', 'Avg Rental Cost', 'Last Rental']);
                    $data = $this->getCustomerStatsForExport($startDate, $endDate);
                    break;
                case 'revenue':
                    fputcsv($file, ['Date', 'Total Revenue', 'Total Payments', 'Avg Payment', 'Unique Customers']);
                    $data = $this->getRevenueStatsForExport($startDate, $endDate);
                    break;
                default:
                    $data = [];
            }

            foreach ($data as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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
        $type = $request->get('type');
        $startDate = $request->get('start_date', now()->subMonth());
        $endDate = $request->get('end_date', now());
        
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
        }

        $html = $this->generatePDFHTML($title, $data, $type, $startDate, $endDate);
        
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        $filename = "reporte_{$type}_" . now()->format('Y-m-d_H-i-s') . '.pdf';
        
        return response()->streamDownload(function() use ($dompdf) {
            echo $dompdf->output();
        }, $filename, ['Content-Type' => 'application/pdf']);
    }

    /**
     * Métodos auxiliares para exportación PDF
     */
    private function getStoreStatsForPDF($startDate, $endDate)
    {
        return Store::select([
                'store.store_id',
                'address.address',
                'city.city',
                DB::raw('COUNT(rental.rental_id) as total_rentals'),
                DB::raw('SUM(payment.amount) as total_revenue'),
                DB::raw('COUNT(DISTINCT rental.customer_id) as unique_customers'),
                DB::raw('AVG(payment.amount) as avg_rental_amount')
            ])
            ->join('address', 'store.address_id', '=', 'address.address_id')
            ->join('city', 'address.city_id', '=', 'city.city_id')
            ->leftJoin('inventory', 'store.store_id', '=', 'inventory.store_id')
            ->leftJoin('rental', 'inventory.inventory_id', '=', 'rental.inventory_id')
            ->leftJoin('payment', 'rental.rental_id', '=', 'payment.rental_id')
            ->whereBetween('rental.rental_date', [$startDate, $endDate])
            ->groupBy(['store.store_id', 'address.address', 'city.city'])
            ->orderBy('total_rentals', 'desc')
            ->get();
    }

    private function getCategoryStatsForPDF($startDate, $endDate)
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
            ->groupBy(['category.category_id', 'category.name'])
            ->orderBy('total_rentals', 'desc')
            ->get();
    }

    private function getCustomerStatsForPDF($startDate, $endDate)
    {
        return Customer::select([
                'customer.first_name',
                'customer.last_name',
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
            ->limit(50)
            ->get();
    }

    private function getRevenueStatsForPDF($startDate, $endDate)
    {
        return DB::table('payment')
            ->select([
                DB::raw("DATE_FORMAT(payment.payment_date, '%Y-%m-%d') as date"),
                DB::raw('SUM(payment.amount) as total_revenue'),
                DB::raw('COUNT(payment.payment_id) as total_payments'),
                DB::raw('AVG(payment.amount) as avg_payment')
            ])
            ->leftJoin('rental', 'payment.rental_id', '=', 'rental.rental_id')
            ->whereBetween('payment.payment_date', [$startDate, $endDate])
            ->groupBy(DB::raw("DATE_FORMAT(payment.payment_date, '%Y-%m-%d')"))
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
