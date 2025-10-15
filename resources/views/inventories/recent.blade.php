@extends('layouts.app')

@section('title', 'Inventario Reciente')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('inventories.index') }}">Inventario</a></li>
                    <li class="breadcrumb-item active">Adiciones Recientes</li>
                </ol>
            </nav>
            <h1 class="display-5 fw-bold text-primary mb-2">
                <i class="fas fa-clock me-3"></i>Inventario Reciente
            </h1>
            <p class="lead text-muted">Artículos agregados en los últimos {{ $days }} días</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('inventories.create') }}" class="btn btn-gradient-primary">
                <i class="fas fa-plus me-2"></i>Agregar Artículo
            </a>
        </div>
    </div>

    <!-- Time Filter -->
    <div class="card shadow-custom border-0 mb-4">
        <div class="card-header bg-white border-bottom">
            <h6 class="mb-0">
                <i class="fas fa-calendar-alt me-2"></i>Filtro de Tiempo
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Período de tiempo</label>
                    <select name="days" class="form-select">
                        <option value="7" {{ $days == 7 ? 'selected' : '' }}>Últimos 7 días</option>
                        <option value="15" {{ $days == 15 ? 'selected' : '' }}>Últimos 15 días</option>
                        <option value="30" {{ $days == 30 ? 'selected' : '' }}>Últimos 30 días</option>
                        <option value="60" {{ $days == 60 ? 'selected' : '' }}>Últimos 60 días</option>
                        <option value="90" {{ $days == 90 ? 'selected' : '' }}>Últimos 90 días</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i>Aplicar
                    </button>
                </div>
                <div class="col-md-7">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('inventories.recent', ['days' => 7]) }}" 
                           class="btn btn-outline-primary btn-sm {{ $days == 7 ? 'active' : '' }}">7d</a>
                        <a href="{{ route('inventories.recent', ['days' => 15]) }}" 
                           class="btn btn-outline-primary btn-sm {{ $days == 15 ? 'active' : '' }}">15d</a>
                        <a href="{{ route('inventories.recent', ['days' => 30]) }}" 
                           class="btn btn-outline-primary btn-sm {{ $days == 30 ? 'active' : '' }}">30d</a>
                        <a href="{{ route('inventories.recent', ['days' => 60]) }}" 
                           class="btn btn-outline-primary btn-sm {{ $days == 60 ? 'active' : '' }}">60d</a>
                        <a href="{{ route('inventories.recent', ['days' => 90]) }}" 
                           class="btn btn-outline-primary btn-sm {{ $days == 90 ? 'active' : '' }}">90d</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-custom border-0">
                <div class="card-header bg-gradient-primary text-white">
                    <h6 class="mb-0">Artículos Agregados</h6>
                </div>
                <div class="card-body text-center">
                    <h2 class="text-primary mb-0">{{ $inventories->total() }}</h2>
                    <small class="text-muted">en {{ $days }} días</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-custom border-0">
                <div class="card-header bg-gradient-success text-white">
                    <h6 class="mb-0">Promedio Diario</h6>
                </div>
                <div class="card-body text-center">
                    <h2 class="text-success mb-0">{{ number_format($inventories->total() / max($days, 1), 1) }}</h2>
                    <small class="text-muted">artículos/día</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-custom border-0">
                <div class="card-header bg-gradient-info text-white">
                    <h6 class="mb-0">Tiendas Activas</h6>
                </div>
                <div class="card-body text-center">
                    <h2 class="text-info mb-0">{{ $inventories->groupBy('store_id')->count() }}</h2>
                    <small class="text-muted">con adiciones</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-custom border-0">
                <div class="card-header bg-gradient-warning text-white">
                    <h6 class="mb-0">Valor Agregado</h6>
                </div>
                <div class="card-body text-center">
                    <h2 class="text-warning mb-0">${{ number_format($inventories->sum(function($item) { return $item->film->replacement_cost ?? 0; }), 0) }}</h2>
                    <small class="text-muted">en inventario</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Timeline -->
    @if($inventories->count() > 0)
        <div class="card shadow-custom border-0 mb-4">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0">
                    <i class="fas fa-chart-line me-2"></i>Actividad Reciente por Día
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @php
                        $dailyStats = $inventories->groupBy(function($item) {
                            return $item->last_update->format('Y-m-d');
                        })->map->count()->sortKeys();
                        
                        $maxCount = $dailyStats->max() ?: 1;
                    @endphp
                    
                    @foreach($dailyStats->take(7) as $date => $count)
                        <div class="col">
                            <div class="text-center">
                                <div class="mb-2">
                                    <div class="bg-primary rounded" 
                                         style="height: {{ ($count / $maxCount) * 100 + 10 }}px; width: 20px; margin: 0 auto;">
                                    </div>
                                </div>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($date)->format('M j') }}</small>
                                <div class="fw-bold text-primary">{{ $count }}</div>
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
                    <i class="fas fa-list me-2 text-primary"></i>Artículos Agregados Recientemente
                </h5>
                <div class="d-flex gap-2">
                    <span class="badge bg-light text-dark">{{ $inventories->total() }} total</span>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($inventories->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Película</th>
                                <th>Tienda</th>
                                <th>Clasificación</th>
                                <th>Tarifa</th>
                                <th>Agregado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inventories as $inventory)
                                <tr>
                                    <td>
                                        <strong class="text-primary">#{{ $inventory->inventory_id }}</strong>
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
                                                <h6 class="mb-1">{{ $inventory->film->title ?? 'Película Desconocida' }}</h6>
                                                @if($inventory->film && $inventory->film->release_year)
                                                    <small class="text-muted">{{ $inventory->film->release_year }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary rounded-circle text-white text-center me-2"
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
                                            <strong class="text-success">${{ number_format($inventory->film->rental_rate, 2) }}</strong>
                                            <small class="text-muted d-block">por día</small>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold text-primary">{{ $inventory->last_update->format('M j, Y') }}</span>
                                            <small class="text-muted">{{ $inventory->last_update->format('g:i A') }}</small>
                                            <small class="text-success">{{ $inventory->last_update->diffForHumans() }}</small>
                                        </div>
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
                                            <form action="{{ route('inventories.destroy', $inventory->inventory_id) }}" 
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('¿Está seguro de eliminar este artículo del inventario?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="card-footer bg-white border-top-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Mostrando {{ $inventories->firstItem() }} - {{ $inventories->lastItem() }} 
                            de {{ $inventories->total() }} resultados
                        </div>
                        {{ $inventories->appends(['days' => $days])->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay artículos recientes</h5>
                    <p class="text-muted">No se han agregado artículos al inventario en los últimos {{ $days }} días.</p>
                    <a href="{{ route('inventories.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Agregar Artículo Ahora
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

.shadow-custom {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.badge {
    font-size: 0.75em;
}

.table td {
    vertical-align: middle;
}

.btn-outline-primary.active {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
}
</style>
@endpush