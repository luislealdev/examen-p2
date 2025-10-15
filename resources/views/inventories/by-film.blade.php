@extends('layouts.app')

@section('title', 'Inventario por Película - ' . $film->title)

@section('content')
<div class="container">
    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('inventories.index') }}">Inventario</a></li>
                    <li class="breadcrumb-item active">Por Película</li>
                </ol>
            </nav>
            <h1 class="display-5 fw-bold text-primary mb-2">
                <i class="fas fa-film me-3"></i>Inventario: {{ $film->title }}
            </h1>
            <div class="row">
                <div class="col-md-8">
                    <p class="lead text-muted mb-1">{{ $film->description }}</p>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge bg-primary">{{ $film->rating }}</span>
                        <span class="badge bg-secondary">{{ $film->release_year }}</span>
                        <span class="badge bg-info">${{ number_format($film->rental_rate, 2) }}/día</span>
                        @if($film->language)
                            <span class="badge bg-success">{{ $film->language->name }}</span>
                        @endif
                        @if($film->category)
                            <span class="badge bg-warning text-dark">{{ $film->category->name }}</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    @if($film->poster_url)
                        <img src="{{ $film->poster_url }}" alt="{{ $film->title }}" 
                             class="img-thumbnail shadow-sm" style="max-height: 150px;">
                    @else
                        <div class="bg-light p-4 rounded shadow-sm text-center" style="height: 150px;">
                            <i class="fas fa-film fa-3x text-muted"></i>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-auto">
            <a href="{{ route('inventories.create', ['film_id' => $film->film_id]) }}" 
               class="btn btn-gradient-primary">
                <i class="fas fa-plus me-2"></i>Agregar al Inventario
            </a>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-custom border-0">
                <div class="card-header bg-gradient-primary text-white">
                    <h6 class="mb-0">Total en Inventario</h6>
                </div>
                <div class="card-body text-center">
                    <h2 class="text-primary mb-0">{{ $inventories->total() }}</h2>
                    <small class="text-muted">copias disponibles</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-custom border-0">
                <div class="card-header bg-gradient-success text-white">
                    <h6 class="mb-0">Tiendas con Stock</h6>
                </div>
                <div class="card-body text-center">
                    <h2 class="text-success mb-0">{{ $inventories->groupBy('store_id')->count() }}</h2>
                    <small class="text-muted">ubicaciones</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-custom border-0">
                <div class="card-header bg-gradient-info text-white">
                    <h6 class="mb-0">Valor Total</h6>
                </div>
                <div class="card-body text-center">
                    <h2 class="text-info mb-0">${{ number_format($inventories->count() * $film->replacement_cost, 2) }}</h2>
                    <small class="text-muted">costo de reemplazo</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Table -->
    <div class="card shadow-custom border-0">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2 text-primary"></i>Copias en Inventario
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
                                <th>ID Inventario</th>
                                <th>Tienda</th>
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
                                            <div class="bg-primary rounded-circle text-white text-center me-2"
                                                 style="width: 30px; height: 30px; line-height: 30px; font-size: 12px;">
                                                {{ $inventory->store_id }}
                                            </div>
                                            <span>Tienda #{{ $inventory->store_id }}</span>
                                        </div>
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
                        {{ $inventories->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No hay copias en inventario</h5>
                    <p class="text-muted">Esta película no tiene copias disponibles en ninguna tienda.</p>
                    <a href="{{ route('inventories.create', ['film_id' => $film->film_id]) }}" 
                       class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Agregar Primera Copia
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
</style>
@endpush