@extends('layouts.app')

@section('title', 'Ítem de Inventario #' . $inventory->inventory_id)

@section('content')
<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('inventories.index') }}">Inventario</a></li>
            <li class="breadcrumb-item active">Ítem #{{ $inventory->inventory_id }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col">
            <div class="d-flex align-items-center mb-2">
                <h1 class="display-5 fw-bold text-gradient me-3">
                    Ítem de Inventario #{{ $inventory->inventory_id }}
                </h1>
                <span class="badge bg-{{ $inventory->status_color }} fs-6">{{ $inventory->status }}</span>
            </div>
            <p class="lead text-muted">
                <i class="fas fa-boxes me-2"></i>{{ $inventory->film_title }} en {{ $inventory->store_location }}
            </p>
        </div>
        <div class="col-auto">
            <div class="btn-group">
                <a href="{{ route('inventories.edit', $inventory) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i>Editar Ítem
                </a>
                <button type="button" class="btn btn-outline-danger" 
                        data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="fas fa-trash me-2"></i>Eliminar
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Inventory Details Card -->
            <div class="card shadow-lg border-0 mb-4">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Detalles del Inventario
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="fw-bold">Información Básica</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td class="fw-bold">ID de Inventario:</td>
                                    <td><span class="text-primary fw-bold">#{{ $inventory->inventory_id }}</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Tienda:</td>
                                    <td>
                                        <a href="{{ route('inventories.by-store', $inventory->store) }}" class="text-decoration-none">
                                            {{ $inventory->store_location }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Estado:</td>
                                    <td>
                                        <span class="badge bg-{{ $inventory->status_color }}">{{ $inventory->status }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Última Actualización:</td>
                                    <td>
                                        <span title="{{ $inventory->last_update_format }}">
                                            {{ $inventory->last_update_human }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h6 class="fw-bold">Información de Renta</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td class="fw-bold">Tarifa de Renta:</td>
                                    <td><span class="text-success fw-bold">${{ number_format($inventory->rental_rate, 2) }}</span></td>
                                </tr>
                                @if($inventory->film)
                                <tr>
                                    <td class="fw-bold">Duración de Renta:</td>
                                    <td>{{ $inventory->film->rental_duration }} {{ Str::plural('día', $inventory->film->rental_duration) }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Costo de Reemplazo:</td>
                                    <td><span class="text-danger fw-bold">${{ number_format($inventory->film->replacement_cost, 2) }}</span></td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="fw-bold">Relación de Valor:</td>
                                    <td>
                                        @if($inventory->film && $inventory->film->replacement_cost > 0)
                                            {{ number_format($inventory->film->replacement_cost / $inventory->rental_rate, 1) }}x
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historial de Rentas Card -->
            <div class="card shadow-lg border-0 mb-4">
                <div class="card-header bg-gradient-success text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-history me-2"></i>Historial de Rentas
                        </h5>
                        <span class="badge bg-light text-dark">{{ $historyStats['total_rentals'] }} rentas totales</span>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Estadísticas del Historial -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-chart-line fa-2x mb-2"></i>
                                    <h4 class="mb-0">{{ $historyStats['total_rentals'] }}</h4>
                                    <small>Total de Rentas</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-clock fa-2x mb-2"></i>
                                    <h4 class="mb-0">{{ $historyStats['active_rentals'] }}</h4>
                                    <small>Rentas Activas</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                                    <h4 class="mb-0">{{ $historyStats['completed_rentals'] }}</h4>
                                    <small>Rentas Completadas</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-dollar-sign fa-2x mb-2"></i>
                                    <h4 class="mb-0">${{ number_format($historyStats['total_revenue'], 2) }}</h4>
                                    <small>Ingresos Totales</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lista del Historial -->
                    @if($rentalHistory->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID Renta</th>
                                        <th>Cliente</th>
                                        <th>Fecha de Renta</th>
                                        <th>Fecha de Devolución</th>
                                        <th>Personal</th>
                                        <th>Estado</th>
                                        <th>Días Rentados</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rentalHistory as $rental)
                                    <tr>
                                        <td>
                                            <span class="fw-bold text-primary">#{{ $rental->rental_id }}</span>
                                        </td>
                                        <td>
                                            {{ $rental->customer->first_name }} {{ $rental->customer->last_name }}
                                            @if($rental->customer->email)
                                                <br><small class="text-muted">{{ $rental->customer->email }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span title="{{ $rental->rental_date->format('d/m/Y H:i:s') }}">
                                                {{ $rental->rental_date->format('d/m/Y') }}
                                                <br><small class="text-muted">{{ $rental->rental_date->format('H:i') }}</small>
                                            </span>
                                        </td>
                                        <td>
                                            @if($rental->return_date)
                                                <span title="{{ $rental->return_date->format('d/m/Y H:i:s') }}">
                                                    {{ $rental->return_date->format('d/m/Y') }}
                                                    <br><small class="text-muted">{{ $rental->return_date->format('H:i') }}</small>
                                                </span>
                                            @else
                                                <span class="badge bg-warning">En curso</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($rental->staff)
                                                {{ $rental->staff->first_name }} {{ $rental->staff->last_name }}
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($rental->return_date)
                                                @php
                                                    $dueDate = $rental->rental_date->addDays($inventory->film->rental_duration ?? 3);
                                                    $isLate = $rental->return_date > $dueDate;
                                                @endphp
                                                @if($isLate)
                                                    <span class="badge bg-danger">Devuelto Tarde</span>
                                                @else
                                                    <span class="badge bg-success">Devuelto a Tiempo</span>
                                                @endif
                                            @else
                                                @php
                                                    $dueDate = $rental->rental_date->addDays($inventory->film->rental_duration ?? 3);
                                                    $isOverdue = now() > $dueDate;
                                                @endphp
                                                @if($isOverdue)
                                                    <span class="badge bg-danger">Vencido</span>
                                                @else
                                                    <span class="badge bg-primary">Activo</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if($rental->return_date)
                                                {{ $rental->rental_date->diffInDays($rental->return_date) }} días
                                            @else
                                                {{ $rental->rental_date->diffInDays(now()) }} días
                                                <small class="text-muted">(en curso)</small>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Sin Historial de Rentas</h5>
                            <p class="text-muted">Este ítem de inventario no ha sido rentado aún.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Film Information Card -->
            @if($inventory->film)
            <div class="card shadow-lg border-0 mb-4">
                <div class="card-header bg-gradient-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-film me-2"></i>Información de la Película
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-start">
                        <div class="col-md-8">
                            <h4 class="fw-bold">{{ $inventory->film->title }}</h4>
                            @if($inventory->film->description)
                                <p class="text-muted">{{ $inventory->film->description }}</p>
                            @endif
                            
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <strong>Idioma:</strong> {{ $inventory->film->language->name ?? 'N/A' }}
                                </div>
                                <div class="col-sm-6">
                                    <strong>Duración:</strong> {{ $inventory->film->duration_format }}
                                </div>
                                @if($inventory->film->release_year)
                                <div class="col-sm-6">
                                    <strong>Año de Lanzamiento:</strong> {{ $inventory->film->release_year }}
                                </div>
                                @endif
                                <div class="col-sm-6">
                                    <strong>Categoría de Edad:</strong> {{ $inventory->film->age_category }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="badge bg-{{ $inventory->film->rating_color }} fs-5 mb-2">
                                {{ $inventory->film->rating }}
                            </span>
                            <br>
                            @if($inventory->film->release_year)
                                <span class="badge bg-secondary fs-6">{{ $inventory->film->release_year }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Category -->
                    @if($inventory->film->category)
                        <div class="mb-2">
                            <strong>Categoría:</strong><br>
                            <span class="badge bg-secondary">{{ $inventory->film->category->name }}</span>
                        </div>
                    @endif
                    <!-- Special Features -->
                    @if($inventory->film->special_features && count($inventory->film->special_features) > 0)
                        <div class="mt-3">
                            <strong>Características Especiales:</strong><br>
                            <div class="d-flex flex-wrap gap-1 mt-2">
                                @foreach($inventory->film->special_features as $feature)
                                    <span class="badge bg-warning text-dark">{{ $feature }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Actions Card -->
            <div class="card shadow-lg border-0 mb-4">
                <div class="card-header bg-gradient-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>Acciones Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('inventories.edit', $inventory) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>Editar Este Ítem
                        </a>
                        
                        @if($inventory->film)
                            <a href="{{ route('inventories.by-film', $inventory->film) }}" class="btn btn-outline-info">
                                <i class="fas fa-film me-2"></i>Todas las Copias de Esta Película
                            </a>
                            <a href="{{ route('films.show', $inventory->film) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-info-circle me-2"></i>Detalles de la Película
                            </a>
                        @endif
                        
                        @if($inventory->store)
                            <a href="{{ route('inventories.by-store', $inventory->store) }}" class="btn btn-outline-warning">
                                <i class="fas fa-store me-2"></i>Inventario de la Tienda
                            </a>
                            <a href="{{ route('stores.show', $inventory->store) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-building me-2"></i>Detalles de la Tienda
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Related Inventory Card -->
            @if($inventory->film)
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-warning text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-copy me-2"></i>Otras Copias
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $otherCopies = \App\Models\Inventory::where('film_id', $inventory->film_id)
                            ->where('inventory_id', '!=', $inventory->inventory_id)
                            ->with('store')
                            ->take(5)
                            ->get();
                    @endphp
                    
                    @if($otherCopies->count() > 0)
                        @foreach($otherCopies as $copy)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <small class="text-muted">#{{ $copy->inventory_id }}</small><br>
                                    <strong>{{ $copy->store_location }}</strong>
                                </div>
                                <div>
                                    <span class="badge bg-{{ $copy->status_color }}">{{ $copy->status }}</span>
                                </div>
                            </div>
                        @endforeach
                        
                        @php
                            $totalCopies = \App\Models\Inventory::where('film_id', $inventory->film_id)->count();
                        @endphp
                        
                        @if($totalCopies > 5)
                            <div class="text-center mt-3">
                                <a href="{{ route('inventories.by-film', $inventory->film) }}" class="btn btn-sm btn-outline-primary">
                                    Ver Todas las {{ $totalCopies }} Copias
                                </a>
                            </div>
                        @endif
                    @else
                        <p class="text-muted text-center">Esta es la única copia de esta película en inventario.</p>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro de que desea eliminar el ítem de inventario <strong>#{{ $inventory->inventory_id }}</strong>?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Esta acción no se puede deshacer. El ítem de inventario será eliminado permanentemente.
                </div>
                <div class="card bg-light">
                    <div class="card-body">
                        <strong>Detalles del Ítem:</strong><br>
                        Película: {{ $inventory->film_title }}<br>
                        Tienda: {{ $inventory->store_location }}<br>
                        Estado: {{ $inventory->status }}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="{{ route('inventories.destroy', $inventory) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Eliminar Ítem
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.text-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
}
</style>
@endsection