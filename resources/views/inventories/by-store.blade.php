@extends('layouts.app')

@section('title', 'Inventario por Tienda - Tienda #' . $store->store_id)

@section('content')
<div class="container">
    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('inventories.index') }}">Inventario</a></li>
                    <li class="breadcrumb-item active">Por Tienda</li>
                </ol>
            </nav>
            <h1 class="display-5 fw-bold text-primary mb-2">
                <i class="fas fa-store me-3"></i>Inventario: Tienda #{{ $store->store_id }}
            </h1>
            <p class="lead text-muted">Gestión completa del inventario de la tienda</p>
        </div>
        <div class="col-auto">
            <div class="btn-group">
                <a href="{{ route('inventories.create', ['store_id' => $store->store_id]) }}" 
                   class="btn btn-gradient-primary">
                    <i class="fas fa-plus me-2"></i>Agregar Artículo
                </a>
                <a href="{{ route('inventories.bulk-create', ['store_id' => $store->store_id]) }}" 
                   class="btn btn-gradient-success">
                    <i class="fas fa-layer-group me-2"></i>Agregar en Lote
                </a>
            </div>
        </div>
    </div>

    <!-- Store Information -->
    <div class="card shadow-custom border-0 mb-4">
        <div class="card-header bg-gradient-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-info-circle me-2"></i>Información de la Tienda
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-muted">ID de Tienda</h6>
                    <p class="fs-5 fw-bold text-primary mb-3">#{{ $store->store_id }}</p>
                    
                    <h6 class="text-muted">Gerente</h6>
                    @if($store->manager)
                        <p class="mb-3">
                            <i class="fas fa-user me-2"></i>
                            {{ $store->manager->first_name }} {{ $store->manager->last_name }}
                            <br>
                            <small class="text-muted">{{ $store->manager->email }}</small>
                        </p>
                    @else
                        <p class="text-muted">No asignado</p>
                    @endif
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted">Última Actualización</h6>
                    <p class="mb-3">
                        <span title="{{ $store->last_update->format('M d, Y \a\t g:i A') }}">
                            {{ $store->last_update->diffForHumans() }}
                        </span>
                    </p>
                    
                    @if($store->address)
                        <h6 class="text-muted">Dirección</h6>
                        <p class="mb-0">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            {{ $store->address->address }}
                            @if($store->address->city)
                                <br><small class="text-muted">{{ $store->address->city }}</small>
                            @endif
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-custom border-0">
                <div class="card-header bg-gradient-primary text-white">
                    <h6 class="mb-0">Total Artículos</h6>
                </div>
                <div class="card-body text-center">
                    <h2 class="text-primary mb-0">{{ $inventories->total() }}</h2>
                    <small class="text-muted">en stock</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-custom border-0">
                <div class="card-header bg-gradient-success text-white">
                    <h6 class="mb-0">Películas Únicas</h6>
                </div>
                <div class="card-body text-center">
                    <h2 class="text-success mb-0">{{ $inventories->groupBy('film_id')->count() }}</h2>
                    <small class="text-muted">títulos distintos</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-custom border-0">
                <div class="card-header bg-gradient-info text-white">
                    <h6 class="mb-0">Valor Promedio</h6>
                </div>
                <div class="card-body text-center">
                    <h2 class="text-info mb-0">${{ number_format($inventories->avg(function($item) { return $item->film->rental_rate ?? 0; }), 2) }}</h2>
                    <small class="text-muted">por artículo</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-custom border-0">
                <div class="card-header bg-gradient-warning text-white">
                    <h6 class="mb-0">Valor Total</h6>
                </div>
                <div class="card-body text-center">
                    <h2 class="text-warning mb-0">${{ number_format($inventories->sum(function($item) { return $item->film->replacement_cost ?? 0; }), 2) }}</h2>
                    <small class="text-muted">inventario</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-custom border-0 mb-4">
        <div class="card-header bg-white border-bottom">
            <h6 class="mb-0">
                <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Buscar película</label>
                    <input type="text" 
                           name="search" 
                           class="form-control" 
                           placeholder="Título o descripción..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Clasificación</label>
                    <select name="rating" class="form-select">
                        <option value="">Todas</option>
                        <option value="G" {{ request('rating') == 'G' ? 'selected' : '' }}>G</option>
                        <option value="PG" {{ request('rating') == 'PG' ? 'selected' : '' }}>PG</option>
                        <option value="PG-13" {{ request('rating') == 'PG-13' ? 'selected' : '' }}>PG-13</option>
                        <option value="R" {{ request('rating') == 'R' ? 'selected' : '' }}>R</option>
                        <option value="NC-17" {{ request('rating') == 'NC-17' ? 'selected' : '' }}>NC-17</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Orden</label>
                    <select name="sort" class="form-select">
                        <option value="alphabetical" {{ request('sort') == 'alphabetical' ? 'selected' : '' }}>Alfabético</option>
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Más reciente</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Más antiguo</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>Filtrar
                        </button>
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <a href="{{ route('inventories.by-store', $store->store_id) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-redo me-2"></i>Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Inventory Table -->
    <div class="card shadow-custom border-0">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2 text-primary"></i>Artículos en Inventario
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
                                <th>Clasificación</th>
                                <th>Tarifa</th>
                                <th>Estado</th>
                                <th>Última Actualización</th>
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
                                        <span class="badge bg-{{ $inventory->status_color }}">
                                            {{ $inventory->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted" title="{{ $inventory->last_update_format }}">
                                            {{ $inventory->last_update_human }}
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
                        {{ $inventories->appends(request()->query())->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay artículos en inventario</h5>
                    <p class="text-muted">Esta tienda no tiene artículos en inventario o no coinciden con los filtros aplicados.</p>
                    <a href="{{ route('inventories.create', ['store_id' => $store->store_id]) }}" 
                       class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Agregar Primer Artículo
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

.btn-gradient-success {
    background: linear-gradient(45deg, #28a745, #1e7e34);
    border: none;
    color: white;
}

.btn-gradient-success:hover {
    background: linear-gradient(45deg, #1e7e34, #155724);
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
</style>
@endpush