@extends('layouts.app')

@section('title', 'Panel de Administración')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col">
            <h1 class="display-5 fw-bold text-primary">
                <i class="fas fa-tachometer-alt me-3"></i>Panel de Administración
            </h1>
            <p class="lead text-muted">Bienvenido, {{ Auth::user()->name }}</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white shadow-custom">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['total_users'] }}</h4>
                            <p class="mb-0">Usuarios</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white shadow-custom">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['total_films'] }}</h4>
                            <p class="mb-0">Películas</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-film fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white shadow-custom">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['total_stores'] }}</h4>
                            <p class="mb-0">Sucursales</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-store fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white shadow-custom">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['total_customers'] }}</h4>
                            <p class="mb-0">Clientes</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-friends fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rental and Revenue Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-danger text-white shadow-custom">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['active_rentals'] ?? 0 }}</h4>
                            <p class="mb-0">Rentas Activas</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-play-circle fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white shadow-custom">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>${{ number_format($stats['total_revenue'] ?? 0, 2) }}</h4>
                            <p class="mb-0">Ingresos Totales</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white shadow-custom">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['rentals_today'] ?? 0 }}</h4>
                            <p class="mb-0">Rentas Hoy</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calendar-day fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-secondary text-white shadow-custom">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['returns_today'] ?? 0 }}</h4>
                            <p class="mb-0">Devoluciones Hoy</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-undo fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reports and Statistics Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h3 class="fw-bold text-dark mb-3">
                <i class="fas fa-chart-bar me-2"></i>Estadísticas y Reportes
            </h3>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Rental Statistics -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Estadísticas de Rentas</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="#" class="btn btn-outline-primary btn-sm" id="btn-stats-stores">
                            <i class="fas fa-store me-2"></i>Por Sucursal
                        </a>
                        <a href="#" class="btn btn-outline-primary btn-sm" id="btn-stats-categories">
                            <i class="fas fa-tags me-2"></i>Por Categoría
                        </a>
                        <a href="#" class="btn btn-outline-primary btn-sm" id="btn-stats-actors">
                            <i class="fas fa-star me-2"></i>Por Actor
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Reports -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-dollar-sign me-2"></i>Reportes de Ingresos</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="#" class="btn btn-outline-success btn-sm" id="btn-revenue-stores">
                            <i class="fas fa-building me-2"></i>Por Tienda
                        </a>
                        <a href="#" class="btn btn-outline-success btn-sm" id="btn-revenue-global">
                            <i class="fas fa-globe me-2"></i>Global
                        </a>
                        <a href="#" class="btn btn-outline-success btn-sm" id="btn-top-customers">
                            <i class="fas fa-crown me-2"></i>Top Clientes
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Export Options -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-download me-2"></i>Exportar Reportes</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-danger btn-sm" id="btn-export-pdf">
                            <i class="fas fa-file-pdf me-2"></i>Exportar PDF
                        </button>
                        <button class="btn btn-outline-success btn-sm" id="btn-export-csv">
                            <i class="fas fa-file-csv me-2"></i>Exportar CSV
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['total_stores'] }}</h4>
                            <p class="mb-0">Tiendas</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-store fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white shadow-custom">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['total_customers'] }}</h4>
                            <p class="mb-0">Clientes</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-friends fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col">
            <div class="card shadow-custom">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>Acciones Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.users') }}" class="btn btn-outline-primary w-100 py-3">
                                <i class="fas fa-users fa-2x d-block mb-2"></i>
                                Gestionar Usuarios
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('films.create') }}" class="btn btn-outline-success w-100 py-3">
                                <i class="fas fa-plus-circle fa-2x d-block mb-2"></i>
                                Agregar Película
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('stores.create') }}" class="btn btn-outline-info w-100 py-3">
                                <i class="fas fa-store-alt fa-2x d-block mb-2"></i>
                                Nueva Tienda
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('films.statistics') }}" class="btn btn-outline-warning w-100 py-3">
                                <i class="fas fa-chart-bar fa-2x d-block mb-2"></i>
                                Ver Estadísticas
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-custom">
                <div class="card-header bg-gradient-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-user-clock me-2"></i>Usuarios Recientes
                    </h6>
                </div>
                <div class="card-body">
                    @if($stats['recent_users']->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($stats['recent_users'] as $user)
                                <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                                    <div>
                                        <strong>{{ $user->name }}</strong><br>
                                        <small class="text-muted">{{ $user->email }}</small>
                                    </div>
                                    <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'employee' ? 'warning' : 'info') }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">No hay usuarios recientes</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-custom">
                <div class="card-header bg-gradient-success text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-film me-2"></i>Películas Recientes
                    </h6>
                </div>
                <div class="card-body">
                    @if($stats['recent_films']->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($stats['recent_films'] as $film)
                                <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                                    <div>
                                        <strong>{{ $film->title }}</strong><br>
                                        <small class="text-muted">{{ $film->release_year ?? 'Sin año' }}</small>
                                    </div>
                                    <span class="badge bg-primary">{{ $film->rating ?? 'Sin rating' }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">No hay películas recientes</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para mostrar estadísticas -->
    <div class="modal fade" id="statsModal" tabindex="-1" aria-labelledby="statsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="statsModalLabel">Estadísticas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="start_date" class="form-label">Fecha Inicio</label>
                            <input type="date" class="form-control" id="start_date" value="{{ now()->subMonth()->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-6">
                            <label for="end_date" class="form-label">Fecha Fin</label>
                            <input type="date" class="form-control" id="end_date" value="{{ now()->format('Y-m-d') }}">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary" id="updateStats">Actualizar</button>
                    <div id="statsContent" class="mt-3">
                        <!-- Content will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para exportaciones -->
    <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exportModalLabel">Exportar Reporte</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="export_type" class="form-label">Tipo de Reporte</label>
                        <select class="form-select" id="export_type">
                            <option value="stores">Estadísticas por Sucursal</option>
                            <option value="categories">Estadísticas por Categoría</option>
                            <option value="customers">Top Clientes</option>
                            <option value="revenue">Reporte de Ingresos</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="export_start_date" class="form-label">Fecha Inicio</label>
                            <input type="date" class="form-control" id="export_start_date" value="{{ now()->subMonth()->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-6">
                            <label for="export_end_date" class="form-label">Fecha Fin</label>
                            <input type="date" class="form-control" id="export_end_date" value="{{ now()->format('Y-m-d') }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" id="downloadCSV">
                        <i class="fas fa-file-csv me-2"></i>Descargar CSV
                    </button>
                    <button type="button" class="btn btn-danger" id="downloadPDF">
                        <i class="fas fa-file-pdf me-2"></i>Descargar PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentStatsType = '';
    const statsModal = new bootstrap.Modal(document.getElementById('statsModal'));
    const exportModal = new bootstrap.Modal(document.getElementById('exportModal'));

    // Event listeners para botones de estadísticas
    document.getElementById('btn-stats-stores').addEventListener('click', function(e) {
        e.preventDefault();
        showStats('stores', 'Estadísticas por Sucursal');
    });

    document.getElementById('btn-stats-categories').addEventListener('click', function(e) {
        e.preventDefault();
        showStats('categories', 'Estadísticas por Categoría');
    });

    document.getElementById('btn-stats-actors').addEventListener('click', function(e) {
        e.preventDefault();
        showStats('actors', 'Estadísticas por Actor');
    });

    document.getElementById('btn-revenue-stores').addEventListener('click', function(e) {
        e.preventDefault();
        showStats('revenue-stores', 'Ingresos por Tienda');
    });

    document.getElementById('btn-revenue-global').addEventListener('click', function(e) {
        e.preventDefault();
        showStats('revenue-global', 'Ingresos Globales');
    });

    document.getElementById('btn-top-customers').addEventListener('click', function(e) {
        e.preventDefault();
        showStats('top-customers', 'Top Clientes');
    });

    // Event listeners para exportaciones
    document.getElementById('btn-export-csv').addEventListener('click', function(e) {
        e.preventDefault();
        exportModal.show();
    });

    document.getElementById('btn-export-pdf').addEventListener('click', function(e) {
        e.preventDefault();
        exportModal.show();
    });

    // Actualizar estadísticas
    document.getElementById('updateStats').addEventListener('click', function() {
        loadStats(currentStatsType);
    });

    // Descargas
    document.getElementById('downloadCSV').addEventListener('click', function() {
        downloadReport('csv');
    });

    document.getElementById('downloadPDF').addEventListener('click', function() {
        downloadReport('pdf');
    });

    function showStats(type, title) {
        currentStatsType = type;
        document.getElementById('statsModalLabel').textContent = title;
        statsModal.show();
        loadStats(type);
    }

    function loadStats(type) {
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const content = document.getElementById('statsContent');
        
        content.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"></div></div>';

        let url = '';
        switch(type) {
            case 'stores':
                url = '{{ route("admin.reports.stores") }}';
                break;
            case 'categories':
                url = '{{ route("admin.reports.categories") }}';
                break;
            case 'actors':
                url = '{{ route("admin.reports.actors") }}';
                break;
            case 'revenue-stores':
                url = '{{ route("admin.reports.revenue.stores") }}';
                break;
            case 'revenue-global':
                url = '{{ route("admin.reports.revenue.global") }}';
                break;
            case 'top-customers':
                url = '{{ route("admin.reports.customers") }}';
                break;
        }

        fetch(`${url}?start_date=${startDate}&end_date=${endDate}`)
            .then(response => response.json())
            .then(data => {
                content.innerHTML = generateTable(data.data, type);
            })
            .catch(error => {
                content.innerHTML = '<div class="alert alert-danger">Error al cargar los datos</div>';
                console.error('Error:', error);
            });
    }

    function generateTable(data, type) {
        if (!data || data.length === 0) {
            return '<div class="alert alert-info">No hay datos disponibles para el período seleccionado</div>';
        }

        let headers = [];
        let rows = [];

        switch(type) {
            case 'stores':
                headers = ['Sucursal', 'Total Rentas', 'Ingresos', 'Clientes Únicos', 'Promedio/Renta'];
                rows = data.map(item => [
                    `Sucursal ${item.store_id}`,
                    item.total_rentals,
                    `$${parseFloat(item.total_revenue || 0).toFixed(2)}`,
                    item.unique_customers,
                    `$${parseFloat(item.avg_rental_amount || 0).toFixed(2)}`
                ]);
                break;
            case 'categories':
                headers = ['Categoría', 'Total Rentas', 'Ingresos', 'Películas', 'Promedio/Renta'];
                rows = data.map(item => [
                    item.name,
                    item.total_rentals,
                    `$${parseFloat(item.total_revenue || 0).toFixed(2)}`,
                    item.films_rented,
                    `$${parseFloat(item.avg_rental_amount || 0).toFixed(2)}`
                ]);
                break;
            case 'actors':
                headers = ['Actor', 'Total Rentas', 'Ingresos', 'Películas', 'Promedio/Renta'];
                rows = data.map(item => [
                    item.actor,
                    item.total_rentals,
                    `$${parseFloat(item.total_revenue || 0).toFixed(2)}`,
                    item.films_count,
                    `$${parseFloat(item.avg_rental_amount || 0).toFixed(2)}`
                ]);
                break;
            case 'top-customers':
                headers = ['Cliente', 'Email', 'Total Rentas', 'Total Gastado', 'Promedio/Renta', 'Última Renta'];
                rows = data.map(item => [
                    `${item.first_name} ${item.last_name}`,
                    item.email,
                    item.total_rentals,
                    `$${parseFloat(item.total_spent || 0).toFixed(2)}`,
                    `$${parseFloat(item.avg_rental_cost || 0).toFixed(2)}`,
                    item.last_rental_date
                ]);
                break;
            default:
                return '<div class="alert alert-warning">Tipo de reporte no soportado</div>';
        }

        let table = '<div class="table-responsive"><table class="table table-striped table-hover">';
        table += '<thead class="table-dark"><tr>';
        headers.forEach(header => {
            table += `<th>${header}</th>`;
        });
        table += '</tr></thead><tbody>';
        
        rows.forEach(row => {
            table += '<tr>';
            row.forEach(cell => {
                table += `<td>${cell}</td>`;
            });
            table += '</tr>';
        });
        
        table += '</tbody></table></div>';
        return table;
    }

    function downloadReport(format) {
        const type = document.getElementById('export_type').value;
        const startDate = document.getElementById('export_start_date').value;
        const endDate = document.getElementById('export_end_date').value;
        
        const url = format === 'csv' 
            ? '{{ route("admin.reports.export.csv") }}'
            : '{{ route("admin.reports.export.pdf") }}';
            
        const params = new URLSearchParams({
            type: type,
            start_date: startDate,
            end_date: endDate
        });
        
        window.open(`${url}?${params.toString()}`, '_blank');
        exportModal.hide();
    }
});
</script>
@endpush
@endsection