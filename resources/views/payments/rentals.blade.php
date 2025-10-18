@extends('layouts.app')

@section('title', 'Mi Historial de Rentas')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="display-5 fw-bold text-primary">
                <i class="fas fa-history me-3"></i>Mi Historial de Rentas
            </h1>
            <p class="lead text-muted">Todas mis películas alquiladas</p>
        </div>
        <div class="col-auto">
            <div class="btn-group">
                <a href="{{ route('payments.index') }}" class="btn btn-outline-success">
                    <i class="fas fa-credit-card me-2"></i>Ver Pagos
                </a>
                <a href="{{ route('payments.pending') }}" class="btn btn-outline-warning">
                    <i class="fas fa-clock me-2"></i>Cargos Pendientes
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-custom border-0 mb-4">
        <div class="card-header bg-gradient-primary text-white border-0">
            <h5 class="mb-0">
                <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('payments.rentals') }}">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label for="date_from" class="form-label fw-bold">Fecha Desde</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" 
                               value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="date_to" class="form-label fw-bold">Fecha Hasta</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" 
                               value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label fw-bold">Estado</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Todos</option>
                            <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>Devueltas</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendientes</option>
                            <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Vencidas</option>
                        </select>
                    </div>
                    <div class="col-md-3">
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
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="w-100">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-1"></i>Filtrar
                            </button>
                        </div>
                    </div>
                </div>
                
                @if(request()->hasAny(['date_from', 'date_to', 'status', 'store_id']))
                    <div class="mt-2">
                        <a href="{{ route('payments.rentals') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times me-1"></i>Limpiar Filtros
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-custom bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="text-white-50 mb-1">Total Rentas</h5>
                            <h3 class="mb-0">{{ $totalRentals }}</h3>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-film"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-custom bg-gradient-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="text-white-50 mb-1">Devueltas</h5>
                            <h3 class="mb-0">{{ $returnedRentals }}</h3>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-custom bg-gradient-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="text-white-50 mb-1">Pendientes</h5>
                            <h3 class="mb-0">{{ $pendingRentals }}</h3>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-custom bg-gradient-danger text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="text-white-50 mb-1">Vencidas</h5>
                            <h3 class="mb-0">{{ $overdueRentals }}</h3>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rentals Table -->
    <div class="card shadow-custom border-0">
        <div class="card-header bg-gradient-dark text-white border-0">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>Historial de Alquileres
            </h5>
        </div>
        <div class="card-body p-0">
            @if($rentals->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Película</th>
                                <th>Fecha Alquiler</th>
                                <th>Duración</th>
                                <th>Fecha Límite</th>
                                <th>Fecha Devolución</th>
                                <th>Estado</th>
                                <th>Tarifa</th>
                                <th>Tienda</th>
                                <th>Empleado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rentals as $rental)
                                @php
                                    $dueDate = $rental->rental_date->addDays($rental->film->rental_duration);
                                    $isReturned = !is_null($rental->return_date);
                                    $isOverdue = !$isReturned && $dueDate->isPast();
                                    $daysLate = $isOverdue ? $dueDate->diffInDays(now()) : 0;
                                    
                                    if ($isReturned) {
                                        $status = 'Devuelta';
                                        $statusClass = 'success';
                                        $statusIcon = 'check-circle';
                                    } elseif ($isOverdue) {
                                        $status = 'Vencida';
                                        $statusClass = 'danger';
                                        $statusIcon = 'exclamation-triangle';
                                    } else {
                                        $status = 'Pendiente';
                                        $statusClass = 'warning';
                                        $statusIcon = 'clock';
                                    }
                                @endphp
                                <tr class="{{ $isOverdue && !$isReturned ? 'table-danger' : '' }}">
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
                                        <div class="fw-bold {{ $isOverdue && !$isReturned ? 'text-danger' : 'text-warning' }}">
                                            {{ $dueDate->format('d/m/Y') }}
                                        </div>
                                        @if($isOverdue && !$isReturned)
                                            <small class="text-danger">{{ $daysLate }} día(s) tarde</small>
                                        @elseif(!$isReturned)
                                            <small class="text-muted">{{ $dueDate->diffInDays(now()) }} día(s) restantes</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($rental->return_date)
                                            <div class="fw-bold text-success">{{ $rental->return_date->format('d/m/Y') }}</div>
                                            <small class="text-muted">{{ $rental->return_date->format('H:i') }}</small>
                                        @else
                                            <span class="text-muted">No devuelta</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $statusClass }}">
                                            <i class="fas fa-{{ $statusIcon }} me-1"></i>{{ $status }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-success">
                                            ${{ number_format($rental->film->rental_rate, 2) }}
                                        </div>
                                        @if($isOverdue && !$isReturned)
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
                                        <small class="text-muted">ID: {{ $rental->staff->staff_id }}</small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="card-footer bg-light border-0">
                    {{ $rentals->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-film text-muted" style="font-size: 4rem;"></i>
                    <h5 class="text-muted mt-3">No se encontraron alquileres</h5>
                    <p class="text-muted">{{ request()->hasAny(['date_from', 'date_to', 'status', 'store_id']) ? 'Intenta ajustar los filtros de búsqueda.' : 'Aún no has alquilado ninguna película.' }}</p>
                    <a href="{{ route('films.index') }}" class="btn btn-primary">
                        <i class="fas fa-film me-2"></i>Explorar Películas
                    </a>
                </div>
            @endif
        </div>
    </div>

    @if($overdueRentals > 0)
        <!-- Warning Notice -->
        <div class="alert alert-warning mt-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-3 fs-4"></i>
                <div>
                    <h6 class="alert-heading mb-1">¡Atención!</h6>
                    <p class="mb-0">Tienes {{ $overdueRentals }} alquiler(es) vencido(s). Por favor, devuelve las películas lo antes posible para evitar cargos adicionales por mora.</p>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #6610f2 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

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