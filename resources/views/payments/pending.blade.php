@extends('layouts.app')

@section('title', 'Cargos Pendientes')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="display-5 fw-bold text-warning">
                <i class="fas fa-clock me-3"></i>Cargos Pendientes
            </h1>
            <p class="lead text-muted">Películas alquiladas que aún no han sido devueltas</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('payments.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-credit-card me-2"></i>Ver Mis Pagos
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-custom border-0 mb-4">
        <div class="card-header bg-gradient-warning text-white border-0">
            <h5 class="mb-0">
                <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('payments.pending') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="overdue_only" name="overdue_only" 
                                   value="1" {{ request('overdue_only') ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-danger" for="overdue_only">
                                Solo mostrar alquileres vencidos
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="store_id" class="form-label fw-bold">Tienda</label>
                        <select class="form-select" id="store_id" name="store_id">
                            <option value="">Todas las tiendas</option>
                            @foreach(\App\Models\Store::with('address')->get() as $store)
                                <option value="{{ $store->store_id }}" 
                                        {{ request('store_id') == $store->store_id ? 'selected' : '' }}>
                                    Tienda {{ $store->store_id }} - {{ $store->address->address }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="w-100">
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="fas fa-search me-1"></i>Filtrar
                            </button>
                        </div>
                    </div>
                </div>
                
                @if(request()->hasAny(['overdue_only', 'store_id']))
                    <div class="mt-2">
                        <a href="{{ route('payments.pending') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times me-1"></i>Limpiar Filtros
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-custom bg-gradient-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="text-white-50 mb-1">Total Pendientes</h5>
                            <h3 class="mb-0">{{ $totalPendingCount }}</h3>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-custom bg-gradient-danger text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="text-white-50 mb-1">Alquileres Vencidos</h5>
                            <h3 class="mb-0">{{ $overdueCount }}</h3>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Rentals Table -->
    <div class="card shadow-custom border-0">
        <div class="card-header bg-gradient-dark text-white border-0">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>Alquileres Pendientes
            </h5>
        </div>
        <div class="card-body p-0">
            @if($pendingRentals->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Película</th>
                                <th>Fecha Alquiler</th>
                                <th>Duración</th>
                                <th>Fecha Límite</th>
                                <th>Estado</th>
                                <th>Tarifa</th>
                                <th>Tienda</th>
                                <th>Empleado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingRentals as $rental)
                                @php
                                    $dueDate = $rental->rental_date->addDays($rental->film->rental_duration);
                                    $isOverdue = $dueDate->isPast();
                                    $daysLate = $isOverdue ? $dueDate->diffInDays(now()) : 0;
                                @endphp
                                <tr class="{{ $isOverdue ? 'table-danger' : '' }}">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($rental->film->poster_url)
                                                <img src="{{ $rental->film->poster_url }}" 
                                                     alt="{{ $rental->film->title }}" 
                                                     class="rounded me-2"
                                                     style="width: 40px; height: 60px; object-fit: cover;"
                                                     onerror="this.onerror=null; this.src='/placeholder-movie.svg';">
                                            @else
                                                <img src="/placeholder-movie.svg" 
                                                     alt="Imagen no disponible" 
                                                     class="rounded me-2"
                                                     style="width: 40px; height: 60px; object-fit: cover;">
                                            @endif
                                            <div>
                                                <a href="{{ route('films.show', $rental->film) }}" 
                                                   class="fw-bold text-decoration-none">
                                                    {{ $rental->film->title }}
                                                </a>
                                                <div class="small text-muted">{{ $rental->film->release_year }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $rental->rental_date->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $rental->rental_date->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $rental->film->rental_duration }} días</span>
                                    </td>
                                    <td>
                                        <div class="fw-bold {{ $isOverdue ? 'text-danger' : 'text-warning' }}">
                                            {{ $dueDate->format('d/m/Y') }}
                                        </div>
                                        @if($isOverdue)
                                            <small class="text-danger">{{ $daysLate }} día(s) tarde</small>
                                        @else
                                            <small class="text-muted">{{ $dueDate->diffInDays(now()) }} día(s) restantes</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($isOverdue)
                                            <span class="badge bg-danger">
                                                <i class="fas fa-exclamation-triangle me-1"></i>Vencido
                                            </span>
                                        @else
                                            <span class="badge bg-success">
                                                <i class="fas fa-clock me-1"></i>Activo
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold text-success">
                                            ${{ number_format($rental->film->rental_rate, 2) }}
                                        </div>
                                        @if($isOverdue)
                                            <small class="text-danger">
                                                + Mora: ${{ number_format($daysLate * 1.50, 2) }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold">Tienda {{ $rental->inventory->store->store_id }}</div>
                                        <small class="text-muted">{{ $rental->inventory->store->address->address }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $rental->staff->first_name }} {{ $rental->staff->last_name }}</div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="card-footer bg-light border-0">
                    {{ $pendingRentals->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                    <h5 class="text-success mt-3">¡Excelente!</h5>
                    <p class="text-muted">{{ request()->hasAny(['overdue_only', 'store_id']) ? 'No hay alquileres que coincidan con los filtros.' : 'No tienes alquileres pendientes.' }}</p>
                </div>
            @endif
        </div>
    </div>

    @if($overdueCount > 0)
        <!-- Warning Notice -->
        <div class="alert alert-warning mt-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-3 fs-4"></i>
                <div>
                    <h6 class="alert-heading mb-1">¡Atención!</h6>
                    <p class="mb-0">Tienes {{ $overdueCount }} alquiler(es) vencido(s). Por favor, devuelve las películas lo antes posible para evitar cargos adicionales por mora.</p>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
.bg-gradient-warning {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
}

.bg-gradient-danger {
    background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
}

.bg-gradient-dark {
    background: linear-gradient(135deg, #343a40 0%, #6c757d 100%);
}

.table-danger {
    --bs-table-bg: rgba(220, 53, 69, 0.1);
}
</style>
@endsection