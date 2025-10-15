@extends('layouts.app')

@section('title', 'Estadísticas de Inventario')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('inventories.index') }}">Inventario</a></li>
                    <li class="breadcrumb-item active">Estadísticas</li>
                </ol>
            </nav>
            <h1 class="display-5 fw-bold text-primary mb-2">
                <i class="fas fa-chart-bar me-3"></i>Estadísticas de Inventario
            </h1>
            <p class="lead text-muted">Análisis completo y métricas del sistema de inventario</p>
        </div>
        <div class="col-auto">
            <div class="btn-group">
                <a href="{{ route('inventories.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Volver al Inventario
                </a>
                <a href="{{ route('inventories.create') }}" class="btn btn-gradient-primary">
                    <i class="fas fa-plus me-2"></i>Agregar Artículo
                </a>
            </div>
        </div>
    </div>

    <!-- Main Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-custom border-0">
                <div class="card-header bg-gradient-primary text-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="mb-0">Total Artículos</h6>
                            <h3 class="mb-0">{{ number_format($stats['total_items']) }}</h3>
                        </div>
                        <i class="fas fa-boxes fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-custom border-0">
                <div class="card-header bg-gradient-success text-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="mb-0">Adiciones Recientes</h6>
                            <h3 class="mb-0">{{ number_format($stats['recent_additions']) }}</h3>
                        </div>
                        <i class="fas fa-clock fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-custom border-0">
                <div class="card-header bg-gradient-warning text-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="mb-0">Alto Valor</h6>
                            <h3 class="mb-0">{{ number_format($stats['high_value_items']) }}</h3>
                        </div>
                        <i class="fas fa-gem fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-custom border-0">
                <div class="card-header bg-gradient-info text-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="mb-0">Tarifa Promedio</h6>
                            <h3 class="mb-0">${{ number_format($stats['avg_rental_rate'], 2) }}</h3>
                        </div>
                        <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Distribution Charts -->
    <div class="row mb-4">
        <!-- Inventory by Store -->
        <div class="col-lg-6">
            <div class="card shadow-custom border-0 h-100">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0">
                        <i class="fas fa-store me-2 text-primary"></i>Distribución por Tienda
                    </h6>
                </div>
                <div class="card-body">
                    @if(count($stats['by_store']) > 0)
                        @foreach($stats['by_store'] as $storeId => $count)
                            @php
                                $percentage = ($count / $stats['total_items']) * 100;
                            @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="fw-medium">Tienda #{{ $storeId }}</span>
                                    <span class="text-muted">{{ number_format($count) }} artículos</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-primary" 
                                         style="width: {{ $percentage }}%"
                                         title="{{ number_format($percentage, 1) }}%">
                                    </div>
                                </div>
                                <small class="text-muted">{{ number_format($percentage, 1) }}% del total</small>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-chart-bar fa-2x mb-2"></i>
                            <p>No hay datos de distribución por tienda</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Inventory by Rating -->
        <div class="col-lg-6">
            <div class="card shadow-custom border-0 h-100">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0">
                        <i class="fas fa-star me-2 text-warning"></i>Distribución por Clasificación
                    </h6>
                </div>
                <div class="card-body">
                    @if(count($stats['by_rating']) > 0)
                        @php
                            $ratingColors = [
                                'G' => 'success',
                                'PG' => 'info', 
                                'PG-13' => 'warning',
                                'R' => 'danger',
                                'NC-17' => 'dark'
                            ];
                        @endphp
                        @foreach($stats['by_rating'] as $rating => $count)
                            @php
                                $percentage = ($count / $stats['total_items']) * 100;
                                $color = $ratingColors[$rating] ?? 'secondary';
                            @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="fw-medium">
                                        <span class="badge bg-{{ $color }} me-2">{{ $rating }}</span>
                                        Clasificación {{ $rating }}
                                    </span>
                                    <span class="text-muted">{{ number_format($count) }} artículos</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-{{ $color }}" 
                                         style="width: {{ $percentage }}%"
                                         title="{{ number_format($percentage, 1) }}%">
                                    </div>
                                </div>
                                <small class="text-muted">{{ number_format($percentage, 1) }}% del total</small>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-star fa-2x mb-2"></i>
                            <p>No hay datos de distribución por clasificación</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Store Inventory Summary -->
    <div class="card shadow-custom border-0 mb-4">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">
                <i class="fas fa-building me-2 text-primary"></i>Resumen Detallado por Tienda
            </h5>
        </div>
        <div class="card-body p-0">
            @if(count($storeInventory) > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tienda</th>
                                <th>Gerente</th>
                                <th>Total Artículos</th>
                                <th>Películas Únicas</th>
                                <th>Eficiencia</th>
                                <th>Última Actualización</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($storeInventory as $storeId => $data)
                                @php
                                    $efficiency = $data['total_items'] > 0 ? ($data['unique_films'] / $data['total_items']) * 100 : 0;
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary rounded-circle text-white text-center me-2"
                                                 style="width: 35px; height: 35px; line-height: 35px; font-size: 14px;">
                                                {{ $storeId }}
                                            </div>
                                            <strong>Tienda #{{ $storeId }}</strong>
                                        </div>
                                    </td>
                                    <td>
                                        @if($data['store'] && $data['store']->manager)
                                            <div>
                                                <div class="fw-medium">{{ $data['store']->manager->first_name }} {{ $data['store']->manager->last_name }}</div>
                                                <small class="text-muted">{{ $data['store']->manager->email }}</small>
                                            </div>
                                        @else
                                            <span class="text-muted">No asignado</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-primary fs-6">{{ number_format($data['total_items']) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success fs-6">{{ number_format($data['unique_films']) }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress me-2" style="width: 60px; height: 6px;">
                                                <div class="progress-bar bg-{{ $efficiency >= 80 ? 'success' : ($efficiency >= 60 ? 'warning' : 'danger') }}" 
                                                     style="width: {{ $efficiency }}%">
                                                </div>
                                            </div>
                                            <small class="text-muted">{{ number_format($efficiency, 1) }}%</small>
                                        </div>
                                    </td>
                                    <td>
                                        @if($data['store'])
                                            <span class="text-muted" title="{{ $data['store']->last_update->format('M d, Y \a\t g:i A') }}">
                                                {{ $data['store']->last_update->diffForHumans() }}
                                            </span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('inventories.by-store', $storeId) }}" 
                                               class="btn btn-outline-primary"
                                               title="Ver Inventario de Tienda">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($data['store'])
                                                <a href="{{ route('stores.show', $data['store']->store_id) }}" 
                                                   class="btn btn-outline-info"
                                                   title="Ver Detalles de Tienda">
                                                    <i class="fas fa-info-circle"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-building fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay datos de tiendas</h5>
                    <p class="text-muted">No se encontraron datos de inventario por tienda.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card shadow-custom border-0">
        <div class="card-header bg-white border-bottom">
            <h6 class="mb-0">
                <i class="fas fa-bolt me-2 text-warning"></i>Acciones Rápidas
            </h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <a href="{{ route('inventories.recent') }}" class="btn btn-outline-success w-100">
                        <i class="fas fa-clock fa-2x mb-2 d-block"></i>
                        <strong>Ver Recientes</strong>
                        <small class="d-block text-muted">Últimas adiciones</small>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('inventories.high-value') }}" class="btn btn-outline-warning w-100">
                        <i class="fas fa-gem fa-2x mb-2 d-block"></i>
                        <strong>Alto Valor</strong>
                        <small class="d-block text-muted">Artículos premium</small>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('inventories.bulk-create') }}" class="btn btn-outline-primary w-100">
                        <i class="fas fa-layer-group fa-2x mb-2 d-block"></i>
                        <strong>Agregar en Lote</strong>
                        <small class="d-block text-muted">Múltiples artículos</small>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('inventories.index') }}" class="btn btn-outline-info w-100">
                        <i class="fas fa-list fa-2x mb-2 d-block"></i>
                        <strong>Ver Todo</strong>
                        <small class="d-block text-muted">Inventario completo</small>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.btn-gradient-primary {
    background: linear-gradient(45deg, #007bff, #0056b3);
    border: none;
    color: white;
}

.btn-gradient-primary:hover {
    background: linear-gradient(45deg, #0056b3, #004085);
    color: white;
}

.shadow-custom {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.progress {
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
}

.table td {
    vertical-align: middle;
}

.btn-outline-success:hover,
.btn-outline-warning:hover,
.btn-outline-primary:hover,
.btn-outline-info:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.card-header.bg-gradient-primary {
    background: linear-gradient(45deg, #007bff, #0056b3) !important;
}

.card-header.bg-gradient-success {
    background: linear-gradient(45deg, #28a745, #1e7e34) !important;
}

.card-header.bg-gradient-warning {
    background: linear-gradient(45deg, #ffc107, #e0a800) !important;
}

.card-header.bg-gradient-info {
    background: linear-gradient(45deg, #17a2b8, #117a8b) !important;
}
</style>
@endpush