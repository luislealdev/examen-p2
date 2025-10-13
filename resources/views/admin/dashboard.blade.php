@extends('layouts.app')

@section('title', 'Dashboard Administrativo - Sistema Sakila')

@section('content')
<div class="container">
    <!-- Header with Title -->
    <div class="row align-items-center mb-4">
        <div class="col">
            <h1 class="display-4 fw-bold text-primary">
                <i class="fas fa-tachometer-alt me-3"></i>Dashboard Administrativo
            </h1>
            <p class="lead text-muted">Panel de control y estadísticas del Sistema Sakila</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.rental-statistics') }}" class="btn btn-gradient-success btn-lg shadow-custom me-2">
                <i class="fas fa-chart-bar me-2"></i>Estadísticas de Rentas
            </a>
            <a href="{{ route('films.index') }}" class="btn btn-gradient-primary btn-lg shadow-custom">
                <i class="fas fa-arrow-left me-2"></i>Volver a Películas
            </a>
        </div>
    </div>
    
    <!-- Stats Overview -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="h3 fw-bold text-dark mb-3">
                <i class="fas fa-chart-line me-2"></i>Resumen General del Sistema
            </h2>
        </div>
    </div>
    
    <div class="row mb-4">
        <!-- Total Films -->
        <div class="col-md-3 mb-3">
            <div class="card shadow-custom border-0">
                <div class="card-header bg-gradient-primary text-white border-0">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">Total Películas</h6>
                            <h3 class="mb-0">{{ number_format($stats['total_films']) }}</h3>
                        </div>
                        <div class="opacity-75">
                            <i class="fas fa-film fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Stores -->
        <div class="col-md-3 mb-3">
            <div class="card shadow-custom border-0">
                <div class="card-header bg-gradient-success text-white border-0">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">Tiendas</h6>
                            <h3 class="mb-0">{{ number_format($stats['total_stores']) }}</h3>
                        </div>
                        <div class="opacity-75">
                            <i class="fas fa-store fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Customers -->
        <div class="col-md-3 mb-3">
            <div class="card shadow-custom border-0">
                <div class="card-header bg-gradient-info text-white border-0">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">Clientes</h6>
                            <h3 class="mb-0">{{ number_format($stats['total_customers']) }}</h3>
                        </div>
                        <div class="opacity-75">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Staff -->
        <div class="col-md-3 mb-3">
            <div class="card shadow-custom border-0">
                <div class="card-header bg-gradient-warning text-white border-0">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">Personal</h6>
                            <h3 class="mb-0">{{ number_format($stats['total_staff']) }}</h3>
                        </div>
                        <div class="opacity-75">
                            <i class="fas fa-user-tie fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    
    <!-- Second Row -->
    <div class="row mb-4">
        <!-- Total Categories -->
        <div class="col-md-3 mb-3">
            <div class="card shadow-custom border-0">
                <div class="card-header" style="background: linear-gradient(45deg, #e74c3c, #c0392b) !important; color: white;">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">Categorías</h6>
                            <h3 class="mb-0">{{ number_format($stats['total_categories']) }}</h3>
                        </div>
                        <div class="opacity-75">
                            <i class="fas fa-tags fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Languages -->
        <div class="col-md-3 mb-3">
            <div class="card shadow-custom border-0">
                <div class="card-header" style="background: linear-gradient(45deg, #9b59b6, #8e44ad) !important; color: white;">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">Idiomas</h6>
                            <h3 class="mb-0">{{ number_format($stats['total_languages']) }}</h3>
                        </div>
                        <div class="opacity-75">
                            <i class="fas fa-language fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Inventory -->
        <div class="col-md-3 mb-3">
            <div class="card shadow-custom border-0">
                <div class="card-header" style="background: linear-gradient(45deg, #f39c12, #d68910) !important; color: white;">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">Inventario</h6>
                            <h3 class="mb-0">{{ number_format($stats['total_inventory']) }}</h3>
                        </div>
                        <div class="opacity-75">
                            <i class="fas fa-boxes fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Actors -->
        <div class="col-md-3 mb-3">
            <div class="card shadow-custom border-0">
                <div class="card-header" style="background: linear-gradient(45deg, #e91e63, #c2185b) !important; color: white;">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">Actores</h6>
                            <h3 class="mb-0">{{ number_format($stats['total_actors']) }}</h3>
                        </div>
                        <div class="opacity-75">
                            <i class="fas fa-theater-masks fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rental Statistics Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="h3 fw-bold text-dark mb-3">
                <i class="fas fa-handshake me-2"></i>Estadísticas de Rentas
            </h2>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Total Rentals -->
        <div class="col-md-3 mb-3">
            <div class="card shadow-custom border-0">
                <div class="card-header" style="background: linear-gradient(45deg, #17a2b8, #138496) !important; color: white;">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">Total Rentas</h6>
                            <h3 class="mb-0">{{ number_format($stats['total_rentals']) }}</h3>
                        </div>
                        <div class="opacity-75">
                            <i class="fas fa-handshake fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Rentals -->
        <div class="col-md-3 mb-3">
            <div class="card shadow-custom border-0">
                <div class="card-header" style="background: linear-gradient(45deg, #28a745, #1e7e34) !important; color: white;">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">Rentas Activas</h6>
                            <h3 class="mb-0">{{ number_format($stats['active_rentals']) }}</h3>
                        </div>
                        <div class="opacity-75">
                            <i class="fas fa-play-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overdue Rentals -->
        <div class="col-md-3 mb-3">
            <div class="card shadow-custom border-0">
                <div class="card-header" style="background: linear-gradient(45deg, #dc3545, #bd2130) !important; color: white;">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">Rentas Vencidas</h6>
                            <h3 class="mb-0">{{ number_format($stats['overdue_rentals']) }}</h3>
                        </div>
                        <div class="opacity-75">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Access to Rental Stats -->
        <div class="col-md-3 mb-3">
            <div class="card shadow-custom border-0">
                <div class="card-header" style="background: linear-gradient(45deg, #6f42c1, #5a32a3) !important; color: white;">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">Ver Más</h6>
                            <a href="{{ route('admin.rental-statistics') }}" class="btn btn-light btn-sm mt-1">
                                <i class="fas fa-chart-bar me-1"></i>Estadísticas Detalladas
                            </a>
                        </div>
                        <div class="opacity-75">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Statistics -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="h3 fw-bold text-dark mb-3">
                <i class="fas fa-chart-bar me-2"></i>Gráficos y Análisis
            </h2>
        </div>
    </div>
    
    <div class="row mb-4">
        <!-- Films by Category Chart -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-custom border-0">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie me-2"></i>
                        Películas por Categoría (Top 5)
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Films by Language Chart -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-custom border-0">
                <div class="card-header bg-gradient-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        Películas por Idioma
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="languageChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Rating Distribution -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-custom border-0">
                <div class="card-header bg-gradient-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-star me-2"></i>
                        Distribución por Rating
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="ratingChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Inventory by Store -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-custom border-0">
                <div class="card-header bg-gradient-warning text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-store me-2"></i>
                        Inventario por Tienda
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="inventoryChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Tables -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="h3 fw-bold text-dark mb-3">
                <i class="fas fa-table me-2"></i>Datos Detallados
            </h2>
        </div>
    </div>
    
    <div class="row mb-4">
        <!-- Recent Films -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-custom border-0">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clock me-2"></i>
                        Películas Agregadas Recientemente
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Título</th>
                                    <th>Año</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentFilms as $film)
                                <tr>
                                    <td class="fw-bold text-primary">#{{ $film->film_id }}</td>
                                    <td>{{ Str::limit($film->title, 30) }}</td>
                                    <td>
                                        @if($film->release_year)
                                            <span class="badge bg-secondary">{{ $film->release_year }}</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-2x mb-2"></i>
                                        <br>No hay películas disponibles
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Longest Films -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-custom border-0">
                <div class="card-header bg-gradient-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-hourglass-end me-2"></i>
                        Películas Más Largas
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Título</th>
                                    <th>Duración</th>
                                    <th>Rating</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($longestFilms as $film)
                                <tr>
                                    <td class="fw-bold">{{ Str::limit($film->title, 25) }}</td>
                                    <td>
                                        @if($film->length)
                                            <span class="badge bg-success">{{ $film->length }} min</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($film->rating)
                                            <span class="badge badge-custom" style="background: linear-gradient(45deg, #3498db, #2980b9); color: white;">
                                                {{ $film->rating }}
                                            </span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-2x mb-2"></i>
                                        <br>No hay películas disponibles
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Information -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-custom border-0">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Información del Sistema
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 col-lg-3 mb-3">
                            <div class="bg-light rounded p-3 text-center">
                                <i class="fas fa-database text-primary fa-2x mb-2"></i>
                                <h6 class="text-muted mb-1">Base de Datos</h6>
                                <span class="fw-bold">{{ $systemInfo['database_size'] }}</span>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3 mb-3">
                            <div class="bg-light rounded p-3 text-center">
                                <i class="fas fa-clock text-success fa-2x mb-2"></i>
                                <h6 class="text-muted mb-1">Duración Promedio</h6>
                                <span class="fw-bold">{{ number_format($systemInfo['average_film_length'], 1) ?? 'N/A' }} min</span>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3 mb-3">
                            <div class="bg-light rounded p-3 text-center">
                                <i class="fas fa-dollar-sign text-warning fa-2x mb-2"></i>
                                <h6 class="text-muted mb-1">Tarifa Promedio</h6>
                                <span class="fw-bold">${{ number_format($systemInfo['average_rental_rate'], 2) ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3 mb-3">
                            <div class="bg-light rounded p-3 text-center">
                                <i class="fas fa-exchange-alt text-info fa-2x mb-2"></i>
                                <h6 class="text-muted mb-1">Costo Reemplazo</h6>
                                <span class="fw-bold">${{ number_format($systemInfo['average_replacement_cost'], 2) ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-info d-flex align-items-center mt-3">
                                <i class="fas fa-sync-alt me-2"></i>
                                <strong>Última Actualización:</strong> &nbsp;{{ now()->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Color scheme matching app theme
        const colors = {
            primary: '#2c3e50',
            secondary: '#3498db', 
            success: '#27ae60',
            warning: '#f39c12',
            danger: '#e74c3c',
            info: '#17a2b8',
            purple: '#9b59b6'
        };

        // Films by Category Chart
        const categoryCtx = document.getElementById('categoryChart');
        if (categoryCtx) {
            const categoryData = {!! json_encode($filmsByCategory->pluck('films_count')) !!};
            const categoryLabels = {!! json_encode($filmsByCategory->pluck('name')) !!};
            
            if (categoryLabels.length > 0 && categoryData.length > 0 && categoryData.some(count => count > 0)) {
                new Chart(categoryCtx, {
                    type: 'doughnut',
                    data: {
                        labels: categoryLabels,
                        datasets: [{
                            data: categoryData,
                            backgroundColor: [
                                colors.primary,
                                colors.secondary,
                                colors.success,
                                colors.warning,
                                colors.danger
                            ],
                            borderWidth: 3,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true
                                }
                            }
                        }
                    }
                });
            } else {
                categoryCtx.getContext('2d').fillText("No hay datos disponibles", 150, 100);
            }
        }

        // Films by Language Chart
        const languageCtx = document.getElementById('languageChart');
        if (languageCtx) {
            new Chart(languageCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($filmsByLanguage->pluck('name')) !!},
                    datasets: [{
                        label: 'Películas',
                        data: {!! json_encode($filmsByLanguage->pluck('films_count')) !!},
                        backgroundColor: colors.secondary,
                        borderColor: colors.primary,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#e9ecef'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        // Rating Chart
        const ratingCtx = document.getElementById('ratingChart');
        if (ratingCtx) {
            new Chart(ratingCtx, {
                type: 'pie',
                data: {
                    labels: {!! json_encode($ratingStats->pluck('rating')) !!},
                    datasets: [{
                        data: {!! json_encode($ratingStats->pluck('count')) !!},
                        backgroundColor: [
                            colors.success,
                            colors.primary,
                            colors.warning,
                            colors.danger,
                            colors.purple
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                padding: 20,
                                usePointStyle: true
                            }
                        }
                    }
                }
            });
        }

        // Inventory by Store Chart
        const inventoryCtx = document.getElementById('inventoryChart');
        if (inventoryCtx) {
            new Chart(inventoryCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($inventoryByStore->pluck('store_id')->map(function($id) { return 'Tienda ' . $id; })) !!},
                    datasets: [{
                        label: 'Items en Inventario',
                        data: {!! json_encode($inventoryByStore->pluck('inventories_count')) !!},
                        backgroundColor: colors.warning,
                        borderColor: colors.danger,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#e9ecef'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush