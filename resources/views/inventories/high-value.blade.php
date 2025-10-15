@extends('layouts.app')

@section('title', 'Inventario de Alto Valor')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('inventories.index') }}">Inventario</a></li>
                    <li class="breadcrumb-item active">Alto Valor</li>
                </ol>
            </nav>
            <h1 class="display-5 fw-bold text-warning mb-2">
                <i class="fas fa-gem me-3"></i>Inventario de Alto Valor
            </h1>
            <p class="lead text-muted">Artículos premium con tarifa de alquiler ≥ $4.00</p>
        </div>
        <div class="col-auto">
            <div class="btn-group">
                <a href="{{ route('inventories.create') }}" class="btn btn-gradient-primary">
                    <i class="fas fa-plus me-2"></i>Agregar Artículo
                </a>
                <a href="{{ route('inventories.statistics') }}" class="btn btn-gradient-info">
                    <i class="fas fa-chart-bar me-2"></i>Ver Estadísticas
                </a>
            </div>
        </div>
    </div>

    <!-- Value Information -->
    <div class="alert alert-warning border-0 shadow-custom mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-info-circle fa-2x me-3"></i>
            <div>
                <h6 class="alert-heading mb-1">Criterios de Alto Valor</h6>
                <p class="mb-0">
                    Se consideran artículos de alto valor aquellos con tarifa de alquiler de $4.00 o superior.
                    Estos artículos requieren manejo especial y representan mayor valor para el negocio.
                </p>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-custom border-0">
                <div class="card-header bg-gradient-warning text-white">
                    <h6 class="mb-0">Artículos de Alto Valor</h6>
                </div>
                <div class="card-body text-center">
                    <h2 class="text-warning mb-0">{{ $inventories->total() }}</h2>
                    <small class="text-muted">artículos premium</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-custom border-0">
                <div class="card-header bg-gradient-success text-white">
                    <h6 class="mb-0">Valor Total</h6>
                </div>
                <div class="card-body text-center">
                    <h2 class="text-success mb-0">${{ number_format($inventories->sum(function($item) { return $item->film->replacement_cost ?? 0; }), 0) }}</h2>
                    <small class="text-muted">en inventario</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-custom border-0">
                <div class="card-header bg-gradient-info text-white">
                    <h6 class="mb-0">Tarifa Promedio</h6>
                </div>
                <div class="card-body text-center">
                    <h2 class="text-info mb-0">${{ number_format($inventories->avg(function($item) { return $item->film->rental_rate ?? 0; }), 2) }}</h2>
                    <small class="text-muted">por día</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-custom border-0">
                <div class="card-header bg-gradient-primary text-white">
                    <h6 class="mb-0">Tiendas Participantes</h6>
                </div>
                <div class="card-body text-center">
                    <h2 class="text-primary mb-0">{{ $inventories->groupBy('store_id')->count() }}</h2>
                    <small class="text-muted">ubicaciones</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Value Distribution -->
    @if($inventories->count() > 0)
        <div class="card shadow-custom border-0 mb-4">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0">
                    <i class="fas fa-chart-pie me-2"></i>Distribución por Rango de Precios
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @php
                        $priceRanges = [
                            '$4.00 - $4.99' => $inventories->filter(function($item) { 
                                $rate = $item->film->rental_rate ?? 0;
                                return $rate >= 4.00 && $rate < 5.00; 
                            })->count(),
                            '$5.00 - $5.99' => $inventories->filter(function($item) { 
                                $rate = $item->film->rental_rate ?? 0;
                                return $rate >= 5.00 && $rate < 6.00; 
                            })->count(),
                            '$6.00+' => $inventories->filter(function($item) { 
                                $rate = $item->film->rental_rate ?? 0;
                                return $rate >= 6.00; 
                            })->count(),
                        ];
                        $maxCount = max(array_values($priceRanges)) ?: 1;
                        $colors = ['primary', 'success', 'warning'];
                    @endphp
                    
                    @foreach($priceRanges as $range => $count)
                        <div class="col-md-4">
                            <div class="text-center mb-3">
                                <div class="mb-2">
                                    <div class="bg-{{ $colors[array_search($range, array_keys($priceRanges))] }} rounded" 
                                         style="height: {{ ($count / $maxCount) * 100 + 20 }}px; width: 40px; margin: 0 auto;">
                                    </div>
                                </div>
                                <h6 class="fw-bold">{{ $range }}</h6>
                                <div class="text-{{ $colors[array_search($range, array_keys($priceRanges))] }} fs-5 fw-bold">{{ $count }}</div>
                                <small class="text-muted">artículos</small>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Top Films by Value -->
    @if($inventories->count() > 0)
        <div class="card shadow-custom border-0 mb-4">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0">
                    <i class="fas fa-trophy me-2"></i>Top 5 Películas Más Valiosas
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @php
                        $topFilms = $inventories->groupBy('film_id')
                            ->map(function($items) {
                                $film = $items->first()->film;
                                return [
                                    'film' => $film,
                                    'count' => $items->count(),
                                    'total_value' => $items->count() * ($film->rental_rate ?? 0)
                                ];
                            })
                            ->sortByDesc('total_value')
                            ->take(5);
                    @endphp
                    
                    @foreach($topFilms as $filmId => $data)
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center p-3 border rounded bg-light">
                                @if($data['film']->poster_url)
                                    <img src="{{ $data['film']->poster_url }}" 
                                         alt="{{ $data['film']->title }}"
                                         class="rounded me-3"
                                         style="width: 50px; height: 75px; object-fit: cover;">
                                @else
                                    <div class="bg-white rounded me-3 d-flex align-items-center justify-content-center"
                                         style="width: 50px; height: 75px;">
                                        <i class="fas fa-film text-muted"></i>
                                    </div>
                                @endif
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $data['film']->title }}</h6>
                                    <div class="d-flex justify-content-between">
                                        <small class="text-muted">{{ $data['count'] }} copias</small>
                                        <strong class="text-success">${{ number_format($data['total_value'], 2) }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Inventory Table -->
    <div class="card shadow-custom border-0">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2 text-warning"></i>Artículos de Alto Valor
                </h5>
                <div class="d-flex gap-2">
                    <span class="badge bg-light text-dark">{{ $inventories->total() }} total</span>
                    <span class="badge bg-warning text-dark">Tarifa ≥ $4.00</span>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($inventories->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-warning">
                            <tr>
                                <th>ID</th>
                                <th>Película</th>
                                <th>Tienda</th>
                                <th>Clasificación</th>
                                <th>Tarifa de Alquiler</th>
                                <th>Costo de Reemplazo</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inventories as $inventory)
                                <tr class="table-warning-subtle">
                                    <td>
                                        <strong class="text-warning">#{{ $inventory->inventory_id }}</strong>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($inventory->film && $inventory->film->poster_url)
                                                <img src="{{ $inventory->film->poster_url }}" 
                                                     alt="{{ $inventory->film->title }}"
                                                     class="rounded me-2"
                                                     style="width: 40px; height: 60px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center"
                                                     style="width: 40px; height: 60px;">
                                                    <i class="fas fa-film text-muted"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-1 fw-bold">{{ $inventory->film->title ?? 'Película Desconocida' }}</h6>
                                                @if($inventory->film && $inventory->film->release_year)
                                                    <small class="text-muted">{{ $inventory->film->release_year }}</small>
                                                @endif
                                                @if($inventory->film && $inventory->film->length)
                                                    <small class="text-muted"> • {{ $inventory->film->length }} min</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-warning rounded-circle text-dark fw-bold text-center me-2"
                                                 style="width: 30px; height: 30px; line-height: 30px; font-size: 12px;">
                                                {{ $inventory->store_id }}
                                            </div>
                                            <span>Tienda #{{ $inventory->store_id }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($inventory->film)
                                            <span class="badge bg-{{ $inventory->film->rating == 'R' ? 'danger' : ($inventory->film->rating == 'PG-13' ? 'warning' : 'success') }}">
                                                {{ $inventory->film->rating }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($inventory->film)
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-dollar-sign text-success me-1"></i>
                                                <strong class="text-success fs-5">${{ number_format($inventory->film->rental_rate, 2) }}</strong>
                                                <small class="text-muted ms-1">por día</small>
                                            </div>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($inventory->film)
                                            <strong class="text-info">${{ number_format($inventory->film->replacement_cost, 2) }}</strong>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $inventory->status_color }} fs-6">
                                            <i class="fas fa-circle me-1"></i>{{ $inventory->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('inventories.show', $inventory->inventory_id) }}" 
                                               class="btn btn-outline-info"
                                               title="Ver Detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('inventories.edit', $inventory->inventory_id) }}" 
                                               class="btn btn-outline-warning"
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('inventories.by-film', $inventory->film_id) }}" 
                                               class="btn btn-outline-primary"
                                               title="Ver Todas las Copias">
                                                <i class="fas fa-film"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="card-footer bg-warning bg-opacity-10 border-top-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Mostrando {{ $inventories->firstItem() }} - {{ $inventories->lastItem() }} 
                            de {{ $inventories->total() }} artículos de alto valor
                        </div>
                        {{ $inventories->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-gem fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay artículos de alto valor</h5>
                    <p class="text-muted">No se encontraron artículos con tarifa de alquiler de $4.00 o superior.</p>
                    <a href="{{ route('inventories.create') }}" class="btn btn-warning">
                        <i class="fas fa-plus me-2"></i>Agregar Artículo Premium
                    </a>
                </div>
            @endif
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

.btn-gradient-info {
    background: linear-gradient(45deg, #17a2b8, #117a8b);
    border: none;
    color: white;
}

.btn-gradient-info:hover {
    background: linear-gradient(45deg, #117a8b, #0c5460);
    color: white;
}

.shadow-custom {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.badge {
    font-size: 0.75em;
}

.table td {
    vertical-align: middle;
}

.table-warning-subtle {
    background-color: rgba(255, 193, 7, 0.05);
}

.table-warning th {
    background-color: rgba(255, 193, 7, 0.25) !important;
}
</style>
@endpush