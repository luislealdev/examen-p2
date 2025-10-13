<?php

namespace App\Http\Controllers;

use App\Models\Film;
use App\Models\Store;
use App\Models\Customer;
use App\Models\Staff;
use App\Models\Language;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Actor;
use App\Models\Director;
use App\Models\Rental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard with system statistics
     */
    public function dashboard()
    {
        // Estadísticas generales del sistema
        $stats = [
            'total_films' => Film::count(),
            'total_stores' => Store::count(),
            'total_customers' => Customer::count(),
            'total_staff' => Staff::count(),
            'total_languages' => Language::count(),
            'total_categories' => Category::count(),
            'total_inventory' => Inventory::count(),
            'total_actors' => Actor::count(),
            'total_directors' => Director::count(),
            'total_rentals' => Rental::count(),
            'active_rentals' => Rental::where('status', 'active')->count(),
            'overdue_rentals' => Rental::where('status', 'overdue')->count(),
        ];

        // Estadísticas de películas por categoría (top 5)
        $filmsByCategory = Category::withCount('films')
            ->orderBy('films_count', 'desc')
            ->take(5)
            ->get();

        // Estadísticas de películas por idioma
        $filmsByLanguage = Language::withCount('films')
            ->orderBy('films_count', 'desc')
            ->take(5)
            ->get();

        // Películas agregadas recientemente (últimas 10)
        $recentFilms = Film::orderBy('film_id', 'desc')
            ->take(10)
            ->get();

        // Estadísticas de rating de películas
        $ratingStats = Film::select('rating', DB::raw('count(*) as count'))
            ->whereNotNull('rating')
            ->groupBy('rating')
            ->orderBy('count', 'desc')
            ->get();

        // Películas más largas (duración)
        $longestFilms = Film::whereNotNull('length')
            ->where('length', '>', 0)
            ->orderBy('length', 'desc')
            ->take(5)
            ->get();

        // Películas más caras (replacement_cost)
        $mostExpensiveFilms = Film::whereNotNull('replacement_cost')
            ->orderBy('replacement_cost', 'desc')
            ->take(5)
            ->get();

        // Inventario por tienda
        $inventoryByStore = Store::withCount('inventories')
            ->orderBy('inventories_count', 'desc')
            ->get();

        // Estadísticas de años de lanzamiento
        $releaseYearStats = Film::select('release_year', DB::raw('count(*) as count'))
            ->whereNotNull('release_year')
            ->groupBy('release_year')
            ->orderBy('release_year', 'desc')
            ->take(10)
            ->get();

        // Sistema de información
        $systemInfo = [
            'database_size' => $this->getDatabaseSize(),
            'last_film_added' => Film::orderBy('film_id', 'desc')->first()?->last_update ?? 'N/A',
            'average_film_length' => Film::whereNotNull('length')->where('length', '>', 0)->avg('length'),
            'average_rental_rate' => Film::whereNotNull('rental_rate')->where('rental_rate', '>', 0)->avg('rental_rate'),
            'average_replacement_cost' => Film::whereNotNull('replacement_cost')->where('replacement_cost', '>', 0)->avg('replacement_cost'),
        ];

        return view('admin.dashboard', compact(
            'stats',
            'filmsByCategory',
            'filmsByLanguage',
            'recentFilms',
            'ratingStats',
            'longestFilms',
            'mostExpensiveFilms',
            'inventoryByStore',
            'releaseYearStats',
            'systemInfo'
        ));
    }

    /**
     * Show rental statistics page
     */
    public function rentalStatistics(Request $request)
    {
        $type = $request->get('type', 'store'); // store, category, actor, revenue, top-customers
        $timeframe = $request->get('timeframe', '30'); // days
        $storeId = $request->get('store_id'); // for filtering by store
        $format = $request->get('format'); // csv, pdf for exports

        $data = [];
        $title = '';
        
        // Handle exports
        if ($format === 'csv') {
            return $this->exportToCSV($request);
        } elseif ($format === 'pdf') {
            return $this->exportToPDF($request);
        }
        
        switch ($type) {
            case 'store':
                $data = $this->getRentalsByStore($timeframe);
                $title = 'Estadísticas de Rentas por Sucursal';
                break;
                
            case 'category':
                $data = $this->getRentalsByCategory($timeframe);
                $title = 'Estadísticas de Rentas por Categoría';
                break;
                
            case 'actor':
                $data = $this->getRentalsByActor($timeframe);
                $title = 'Estadísticas de Rentas por Actor';
                break;
                
            case 'revenue':
                $data = $this->getRevenueData($timeframe, $storeId);
                $title = $storeId ? 'Reporte de Ingresos - Tienda #' . $storeId : 'Reporte de Ingresos Global';
                break;
                
            case 'top-customers':
                $data = $this->getTopCustomersData($timeframe, $storeId, 10);
                $title = 'Clientes con Mayor Número de Rentas';
                break;
        }

        // Estadísticas generales de rentas
        $generalStats = $this->getGeneralRentalStats($timeframe);
        
        // Get all stores for filter dropdown
        $stores = Store::all();
        
        return view('admin.rental-statistics', compact('data', 'title', 'type', 'timeframe', 'generalStats', 'stores', 'storeId'));
    }

    /**
     * Get rentals statistics by store
     */
    private function getRentalsByStore($timeframe)
    {
        return Store::whereHas('inventories.rentals', function ($query) use ($timeframe) {
                $query->where('rental_date', '>=', now()->subDays($timeframe));
            })
            ->withCount(['inventories as total_rentals' => function ($query) use ($timeframe) {
                $query->join('rentals', 'inventory.inventory_id', '=', 'rentals.inventory_id')
                      ->where('rentals.rental_date', '>=', now()->subDays($timeframe));
            }])
            ->withSum(['inventories as total_revenue' => function ($query) use ($timeframe) {
                $query->join('rentals', 'inventory.inventory_id', '=', 'rentals.inventory_id')
                      ->where('rentals.rental_date', '>=', now()->subDays($timeframe));
            }], 'rentals.rental_amount')
            ->orderBy('total_rentals', 'desc')
            ->get()
            ->map(function ($store) {
                return [
                    'name' => $store->store_name ?? "Sucursal #{$store->store_id}",
                    'location' => $store->address ?? 'N/A',
                    'total_rentals' => $store->total_rentals ?? 0,
                    'total_revenue' => number_format($store->total_revenue ?? 0, 2),
                    'avg_per_rental' => $store->total_rentals > 0 ? number_format(($store->total_revenue ?? 0) / $store->total_rentals, 2) : '0.00'
                ];
            });
    }

    /**
     * Get rentals statistics by category
     */
    private function getRentalsByCategory($timeframe)
    {
        return Category::whereHas('films.inventories.rentals', function ($query) use ($timeframe) {
                $query->where('rental_date', '>=', now()->subDays($timeframe));
            })
            ->withCount(['films as total_rentals' => function ($query) use ($timeframe) {
                $query->join('inventory', 'film.film_id', '=', 'inventory.film_id')
                      ->join('rentals', 'inventory.inventory_id', '=', 'rentals.inventory_id')
                      ->where('rentals.rental_date', '>=', now()->subDays($timeframe));
            }])
            ->withSum(['films as total_revenue' => function ($query) use ($timeframe) {
                $query->join('inventory', 'film.film_id', '=', 'inventory.film_id')
                      ->join('rentals', 'inventory.inventory_id', '=', 'rentals.inventory_id')
                      ->where('rentals.rental_date', '>=', now()->subDays($timeframe));
            }], 'rentals.rental_amount')
            ->orderBy('total_rentals', 'desc')
            ->get()
            ->map(function ($category) {
                return [
                    'name' => $category->name,
                    'total_rentals' => $category->total_rentals ?? 0,
                    'total_revenue' => number_format($category->total_revenue ?? 0, 2),
                    'avg_per_rental' => $category->total_rentals > 0 ? number_format(($category->total_revenue ?? 0) / $category->total_rentals, 2) : '0.00'
                ];
            });
    }

    /**
     * Get rentals statistics by actor
     */
    private function getRentalsByActor($timeframe)
    {
        return Actor::whereHas('films.inventories.rentals', function ($query) use ($timeframe) {
                $query->where('rental_date', '>=', now()->subDays($timeframe));
            })
            ->withCount(['films as total_rentals' => function ($query) use ($timeframe) {
                $query->join('inventory', 'film.film_id', '=', 'inventory.film_id')
                      ->join('rentals', 'inventory.inventory_id', '=', 'rentals.inventory_id')
                      ->where('rentals.rental_date', '>=', now()->subDays($timeframe));
            }])
            ->withSum(['films as total_revenue' => function ($query) use ($timeframe) {
                $query->join('inventory', 'film.film_id', '=', 'inventory.film_id')
                      ->join('rentals', 'inventory.inventory_id', '=', 'rentals.inventory_id')
                      ->where('rentals.rental_date', '>=', now()->subDays($timeframe));
            }], 'rentals.rental_amount')
            ->orderBy('total_rentals', 'desc')
            ->take(20) // Limitar a top 20 actores
            ->get()
            ->map(function ($actor) {
                return [
                    'name' => "{$actor->first_name} {$actor->last_name}",
                    'total_rentals' => $actor->total_rentals ?? 0,
                    'total_revenue' => number_format($actor->total_revenue ?? 0, 2),
                    'avg_per_rental' => $actor->total_rentals > 0 ? number_format(($actor->total_revenue ?? 0) / $actor->total_rentals, 2) : '0.00'
                ];
            });
    }

    /**
     * Get general rental statistics
     */
    private function getGeneralRentalStats($timeframe)
    {
        $baseQuery = Rental::where('rental_date', '>=', now()->subDays($timeframe));
        
        return [
            'total_rentals' => (clone $baseQuery)->count(),
            'total_revenue' => (clone $baseQuery)->sum('rental_amount'),
            'active_rentals' => (clone $baseQuery)->where('status', 'active')->count(),
            'returned_rentals' => (clone $baseQuery)->where('status', 'returned')->count(),
            'overdue_rentals' => (clone $baseQuery)->where('status', 'overdue')->count(),
            'avg_rental_amount' => (clone $baseQuery)->avg('rental_amount'),
            'top_customer' => $this->getTopCustomer($timeframe),
            'busiest_day' => $this->getBusiestDay($timeframe),
        ];
    }

    /**
     * Get top customer in timeframe
     */
    private function getTopCustomer($timeframe)
    {
        $customer = Customer::whereHas('rentals', function ($query) use ($timeframe) {
                $query->where('rental_date', '>=', now()->subDays($timeframe));
            })
            ->withCount(['rentals as rental_count' => function ($query) use ($timeframe) {
                $query->where('rental_date', '>=', now()->subDays($timeframe));
            }])
            ->orderBy('rental_count', 'desc')
            ->first();

        return $customer ? "{$customer->first_name} {$customer->last_name} ({$customer->rental_count} rentas)" : 'N/A';
    }

    /**
     * Get busiest rental day
     */
    private function getBusiestDay($timeframe)
    {
        $result = Rental::selectRaw('DATE(rental_date) as rental_day, COUNT(*) as rental_count')
            ->where('rental_date', '>=', now()->subDays($timeframe))
            ->groupBy('rental_day')
            ->orderBy('rental_count', 'desc')
            ->first();

        return $result ? "{$result->rental_day} ({$result->rental_count} rentas)" : 'N/A';
    }

    /**
     * Get database size information
     */
    private function getDatabaseSize()
    {
        try {
            $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table'");
            return count($tables) . ' tables';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    /**
     * Generate revenue report (legacy method)
     */
    private function getRevenueReportLegacy($timeframe, $storeId = null)
    {
        $query = DB::table('rentals')
            ->join('inventory', 'rentals.inventory_id', '=', 'inventory.inventory_id')
            ->join('stores', 'inventory.store_id', '=', 'stores.store_id')
            ->where('rentals.rental_date', '>=', now()->subDays($timeframe));

        if ($storeId) {
            $query->where('stores.store_id', $storeId);
        }

        $revenueByStore = $query
            ->selectRaw('stores.store_id, 
                         COUNT(rentals.rental_id) as total_rentals,
                         SUM(rentals.rental_amount) as total_revenue,
                         SUM(rentals.late_fee) as total_late_fees,
                         (SUM(rentals.rental_amount) + SUM(rentals.late_fee)) as grand_total,
                         AVG(rentals.rental_amount) as avg_rental_amount')
            ->groupBy('stores.store_id')
            ->orderBy('grand_total', 'desc')
            ->get();

        return $revenueByStore;
    }

    /**
     * Get top customers by rental count
     */
    private function getTopCustomersLegacy($timeframe, $storeId = null)
    {
        $query = DB::table('customers')
            ->join('rentals', 'customers.customer_id', '=', 'rentals.customer_id')
            ->join('inventory', 'rentals.inventory_id', '=', 'inventory.inventory_id')
            ->where('rentals.rental_date', '>=', now()->subDays($timeframe));

        if ($storeId) {
            $query->where('inventory.store_id', $storeId);
        }

        $topCustomers = $query
            ->selectRaw('customers.customer_id,
                         customers.first_name,
                         customers.last_name, 
                         customers.email,
                         customers.store_id as customer_store_id,
                         COUNT(rentals.rental_id) as total_rentals,
                         SUM(rentals.rental_amount) as total_spent,
                         SUM(rentals.late_fee) as total_late_fees,
                         MAX(rentals.rental_date) as last_rental_date')
            ->groupBy('customers.customer_id', 'customers.first_name', 'customers.last_name', 'customers.email', 'customers.store_id')
            ->orderBy('total_rentals', 'desc')
            ->limit(50)
            ->get();

        return $topCustomers;
    }

    /**
     * API endpoint for revenue report
     */
    public function getRevenueReport(Request $request)
    {
        $timeframe = $request->get('timeframe', '30');
        $storeId = $request->get('store_id');

        $data = $this->getRevenueData($timeframe, $storeId);

        return response()->json([
            'success' => true,
            'data' => $data,
            'period' => $this->getPeriodLabel($timeframe),
            'store_id' => $storeId
        ]);
    }

    /**
     * API endpoint for top customers report
     */
    public function getTopCustomers(Request $request)
    {
        $timeframe = $request->get('timeframe', '30');
        $storeId = $request->get('store_id');
        $limit = $request->get('limit', 10);

        $customers = $this->getTopCustomersData($timeframe, $storeId, $limit);

        return response()->json([
            'success' => true,
            'data' => $customers,
            'period' => $this->getPeriodLabel($timeframe),
            'store_id' => $storeId,
            'limit' => $limit
        ]);
    }

    /**
     * Export data to CSV format
     */
    public function exportToCSV(Request $request)
    {
        $type = $request->get('report_type', 'revenue');
        $timeframe = $request->get('timeframe', '30');
        $storeId = $request->get('store_id');

        $data = $this->getDataForExport($type, $timeframe, $storeId);
        $filename = $this->generateFilename($type, $timeframe, $storeId, 'csv');

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($data, $type) {
            $file = fopen('php://output', 'w');
            
            // Write CSV headers based on report type
            $this->writeCsvHeaders($file, $type);
            
            // Write data rows
            foreach ($data as $row) {
                $this->writeCsvRow($file, $row, $type);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export data to PDF format
     */
    public function exportToPDF(Request $request)
    {
        $type = $request->get('report_type', 'revenue');
        $timeframe = $request->get('timeframe', '30');
        $storeId = $request->get('store_id');
        $limit = $request->get('limit', 10);

        try {
            // Prepare data for PDF
            if ($type === 'revenue') {
                $revenueData = $this->getRevenueData($timeframe, $storeId);
                $data = [
                    'revenueByStore' => $revenueData['byStore'],
                    'revenueByMonth' => $revenueData['byMonth'] ?? collect(),
                    'totalRevenue' => $revenueData['totalRevenue'],
                    'totalRentals' => $revenueData['totalRentals'],
                    'totalLateFees' => $revenueData['totalLateFees'],
                    'averagePerRental' => $revenueData['averagePerRental'],
                    'period' => $this->getPeriodLabel($timeframe)
                ];
                $viewName = 'admin.pdf.revenue-report';
            } else {
                $customers = $this->getTopCustomersData($timeframe, $storeId, $limit);
                $data = [
                    'customers' => $customers,
                    'period' => $this->getPeriodLabel($timeframe)
                ];
                $viewName = 'admin.pdf.customers-report';
            }

            $pdf = Pdf::loadView($viewName, $data);
            $filename = $this->generateFilename($type, $timeframe, $storeId, 'pdf');
            
            return $pdf->download($filename);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error generando PDF: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get data formatted for export
     */
    private function getDataForExport($type, $timeframe, $storeId = null)
    {
        switch ($type) {
            case 'revenue':
                return $this->getRevenueData($timeframe, $storeId);
            case 'top-customers':
                return $this->getTopCustomersData($timeframe, $storeId, 50);
            case 'store':
                return $this->getRentalsByStore($timeframe);
            default:
                return collect();
        }
    }

    /**
     * Generate filename for exports
     */
    private function generateFilename($type, $timeframe, $storeId, $extension)
    {
        $typeNames = [
            'revenue' => 'ingresos',
            'top-customers' => 'top-clientes',
            'store' => 'rentas-por-tienda'
        ];

        $typeName = $typeNames[$type] ?? $type;
        $store = $storeId ? "-tienda{$storeId}" : "-global";
        $date = now()->format('Y-m-d');
        
        return "reporte-{$typeName}{$store}-{$timeframe}dias-{$date}.{$extension}";
    }

    /**
     * Write CSV headers
     */
    private function writeCsvHeaders($file, $type)
    {
        switch ($type) {
            case 'revenue':
                fputcsv($file, ['Tienda ID', 'Total Rentas', 'Ingresos Rentas', 'Multas', 'Total General', 'Promedio Renta']);
                break;
            case 'top-customers':
                fputcsv($file, ['Cliente ID', 'Nombre', 'Apellido', 'Email', 'Tienda', 'Total Rentas', 'Total Gastado', 'Multas', 'Última Renta']);
                break;
            case 'store':
                fputcsv($file, ['Tienda', 'Total Rentas']);
                break;
        }
    }

    /**
     * Write CSV row
     */
    private function writeCsvRow($file, $row, $type)
    {
        switch ($type) {
            case 'revenue':
                fputcsv($file, [
                    $row->store_id,
                    $row->total_rentals,
                    number_format($row->total_revenue, 2),
                    number_format($row->total_late_fees, 2),
                    number_format($row->grand_total, 2),
                    number_format($row->avg_rental_amount, 2)
                ]);
                break;
            case 'top-customers':
                fputcsv($file, [
                    $row->customer_id,
                    $row->first_name,
                    $row->last_name,
                    $row->email,
                    $row->customer_store_id,
                    $row->total_rentals,
                    number_format($row->total_spent, 2),
                    number_format($row->total_late_fees, 2),
                    $row->last_rental_date
                ]);
                break;
            case 'store':
                fputcsv($file, [$row->store_name ?? "Tienda {$row->store_id}", $row->rental_count]);
                break;
        }
    }

    /**
     * Generate HTML for PDF export
     */
    private function generatePdfHtml($data, $type, $timeframe, $storeId)
    {
        $title = $this->getReportTitle($type, $storeId);
        $date = now()->format('d/m/Y H:i');
        
        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <title>{$title}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .info { margin-bottom: 20px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; font-weight: bold; }
                .text-right { text-align: right; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>Sakila Video Store</h1>
                <h2>{$title}</h2>
            </div>
            <div class='info'>
                <p><strong>Fecha del reporte:</strong> {$date}</p>
                <p><strong>Período:</strong> Últimos {$timeframe} días</p>
        ";

        if ($storeId) {
            $html .= "<p><strong>Tienda:</strong> #{$storeId}</p>";
        }

        $html .= "</div>";
        $html .= $this->generateTableHtml($data, $type);
        $html .= "</body></html>";

        return $html;
    }

    /**
     * Generate table HTML for PDF
     */
    private function generateTableHtml($data, $type)
    {
        $html = "<table>";
        
        switch ($type) {
            case 'revenue':
                $html .= "<thead><tr>
                    <th>Tienda ID</th>
                    <th>Total Rentas</th>
                    <th>Ingresos Rentas</th>
                    <th>Multas</th>
                    <th>Total General</th>
                    <th>Promedio Renta</th>
                </tr></thead><tbody>";
                
                foreach ($data as $row) {
                    $html .= "<tr>
                        <td>{$row->store_id}</td>
                        <td>{$row->total_rentals}</td>
                        <td class='text-right'>$" . number_format($row->total_revenue, 2) . "</td>
                        <td class='text-right'>$" . number_format($row->total_late_fees, 2) . "</td>
                        <td class='text-right'>$" . number_format($row->grand_total, 2) . "</td>
                        <td class='text-right'>$" . number_format($row->avg_rental_amount, 2) . "</td>
                    </tr>";
                }
                break;
                
            case 'top-customers':
                $html .= "<thead><tr>
                    <th>Cliente</th>
                    <th>Email</th>
                    <th>Tienda</th>
                    <th>Total Rentas</th>
                    <th>Total Gastado</th>
                    <th>Última Renta</th>
                </tr></thead><tbody>";
                
                foreach ($data as $row) {
                    $html .= "<tr>
                        <td>{$row->first_name} {$row->last_name}</td>
                        <td>{$row->email}</td>
                        <td>{$row->customer_store_id}</td>
                        <td>{$row->total_rentals}</td>
                        <td class='text-right'>$" . number_format($row->total_spent, 2) . "</td>
                        <td>{$row->last_rental_date}</td>
                    </tr>";
                }
                break;
        }
        
        $html .= "</tbody></table>";
        return $html;
    }

    /**
     * Get report title
     */
    private function getReportTitle($type, $storeId)
    {
        $titles = [
            'revenue' => $storeId ? "Reporte de Ingresos - Tienda #{$storeId}" : 'Reporte de Ingresos Global',
            'top-customers' => 'Clientes con Mayor Número de Rentas',
            'store' => 'Rentas por Tienda'
        ];

        return $titles[$type] ?? 'Reporte de Estadísticas';
    }

    /**
     * Get revenue data for reports
     */
    private function getRevenueData($timeframe, $storeId = null)
    {
        $query = Rental::with(['inventory.store', 'customer']);

        // Apply date filter
        $query = $this->applyDateFilter($query, $timeframe);

        // Apply store filter
        if ($storeId) {
            $query->whereHas('inventory.store', function($q) use ($storeId) {
                $q->where('store_id', $storeId);
            });
        }

        $rentals = $query->get();

        // Group by store
        $byStore = $rentals->groupBy(function($rental) {
            return $rental->inventory->store->store_id;
        })->map(function($storeRentals, $storeId) {
            $store = Store::with('manager')->find($storeId);
            return (object) [
                'store_id' => $storeId,
                'store_name' => "Tienda #{$storeId}",
                'manager_name' => $store->manager ? $store->manager->first_name . ' ' . $store->manager->last_name : 'N/A',
                'total_rentals' => $storeRentals->count(),
                'rental_revenue' => $storeRentals->sum('rental_amount'),
                'late_fees' => $storeRentals->sum('late_fee'),
                'total_revenue' => $storeRentals->sum('rental_amount') + $storeRentals->sum('late_fee')
            ];
        });

        return [
            'byStore' => $byStore,
            'totalRevenue' => $rentals->sum('rental_amount') + $rentals->sum('late_fee'),
            'totalRentals' => $rentals->count(),
            'totalLateFees' => $rentals->sum('late_fee'),
            'averagePerRental' => $rentals->count() > 0 ? ($rentals->sum('rental_amount') / $rentals->count()) : 0
        ];
    }

    /**
     * Get top customers data for reports
     */
    private function getTopCustomersData($timeframe, $storeId = null, $limit = 10)
    {
        $query = Customer::select('customers.*')
            ->selectRaw('COUNT(rentals.rental_id) as total_rentals')
            ->selectRaw('SUM(rentals.rental_amount + COALESCE(rentals.late_fee, 0)) as total_amount')
            ->selectRaw('MAX(rentals.rental_date) as last_rental')
            ->selectRaw("CONCAT(customers.first_name, ' ', customers.last_name) as full_name")
            ->leftJoin('rentals', 'customers.customer_id', '=', 'rentals.customer_id');

        // Apply date filter to rentals
        $query = $this->applyDateFilter($query, $timeframe, 'rentals');

        // Apply store filter
        if ($storeId) {
            $query->where('customers.store_id', $storeId);
        }

        return $query->groupBy('customers.customer_id')
            ->orderBy('total_rentals', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get period label for display
     */
    private function getPeriodLabel($timeframe)
    {
        $labels = [
            '7' => 'Últimos 7 días',
            '15' => 'Últimos 15 días',
            '30' => 'Últimos 30 días',
            'month' => 'Mes actual',
            'year' => 'Año actual'
        ];

        return $labels[$timeframe] ?? "Últimos {$timeframe} días";
    }

    /**
     * Apply date filter to query
     */
    private function applyDateFilter($query, $timeframe, $table = null)
    {
        $dateField = $table ? "{$table}.rental_date" : 'rental_date';

        switch ($timeframe) {
            case '7':
                $query->where($dateField, '>=', now()->subDays(7));
                break;
            case '15':
                $query->where($dateField, '>=', now()->subDays(15));
                break;
            case '30':
                $query->where($dateField, '>=', now()->subDays(30));
                break;
            case 'month':
                $query->whereYear($dateField, now()->year)
                      ->whereMonth($dateField, now()->month);
                break;
            case 'year':
                $query->whereYear($dateField, now()->year);
                break;
            default:
                if (is_numeric($timeframe)) {
                    $query->where($dateField, '>=', now()->subDays($timeframe));
                }
        }

        return $query;
    }
}