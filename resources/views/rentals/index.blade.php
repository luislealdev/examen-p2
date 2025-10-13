@extends('layouts.app')

@section('title', 'Gestión de Alquileres - Admin')

@section('content')
<div class="container">
    <!-- Header with Title and Actions -->
    <div class="row align-items-center mb-4">
        <div class="col">
            <h1 class="display-4 fw-bold text-primary">
                <i class="fas fa-ticket-alt me-3"></i>Gestión de Alquileres
            </h1>
            <p class="lead text-muted">Administración completa de alquileres del sistema</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('rentals.create') }}" class="btn btn-gradient-success btn-lg shadow-custom me-2">
                <i class="fas fa-plus me-2"></i>Nuevo Alquiler
            </a>
            <a href="{{ route('rentals.overdue') }}" class="btn btn-gradient-warning btn-lg shadow-custom me-2">
                <i class="fas fa-clock me-2"></i>Alquileres Atrasados
            </a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-gradient-primary btn-lg shadow-custom">
                <i class="fas fa-arrow-left me-2"></i>Dashboard
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-gradient-info text-white">
            <h5 class="mb-0">
                <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="status" class="form-label">Estado</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Activo</option>
                        <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Devuelto</option>
                        <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Atrasado</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="store_id" class="form-label">Tienda</label>
                    <select name="store_id" id="store_id" class="form-select">
                        <option value="">Todas las tiendas</option>
                        @foreach($stores as $store)
                            <option value="{{ $store->store_id }}" {{ request('store_id') == $store->store_id ? 'selected' : '' }}>
                                Tienda {{ $store->store_id }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="customer_search" class="form-label">Cliente</label>
                    <input type="text" name="customer_search" id="customer_search" class="form-control" 
                           value="{{ request('customer_search') }}" placeholder="Buscar cliente...">
                </div>
                <div class="col-md-3">
                    <label for="film_search" class="form-label">Película</label>
                    <input type="text" name="film_search" id="film_search" class="form-control" 
                           value="{{ request('film_search') }}" placeholder="Buscar película...">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-gradient-primary">
                        <i class="fas fa-search me-2"></i>Filtrar
                    </button>
                    <a href="{{ route('rentals.index') }}" class="btn btn-outline-secondary ms-2">
                        <i class="fas fa-times me-2"></i>Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Rentals Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>Lista de Alquileres
            </h5>
            <span class="badge bg-light text-dark">{{ $rentals->total() }} registros</span>
        </div>
        <div class="card-body">
            @if($rentals->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>No se encontraron alquileres con los criterios especificados.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Película</th>
                                <th>Tienda</th>
                                <th>F. Alquiler</th>
                                <th>F. Vencimiento</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rentals as $rental)
                                <tr>
                                    <td>#{{ $rental->rental_id }}</td>
                                    <td>
                                        <div>
                                            <strong>{{ $rental->customer->first_name }} {{ $rental->customer->last_name }}</strong>
                                            <br><small class="text-muted">{{ $rental->customer->email }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $rental->inventory->film->poster_url ?? asset('images/default-poster.jpg') }}" 
                                                 alt="Poster" class="me-2" style="width: 40px; height: 60px; object-fit: cover;">
                                            <div>
                                                <strong>{{ $rental->inventory->film->title }}</strong>
                                                <br><small class="text-muted">{{ $rental->inventory->film->release_year }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Tienda {{ $rental->inventory->store_id }}</td>
                                    <td>{{ $rental->rental_date->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($rental->due_date)
                                            {{ $rental->due_date->format('d/m/Y') }}
                                            @if($rental->due_date < now() && $rental->status !== 'returned')
                                                <br><small class="text-danger"><i class="fas fa-exclamation-triangle"></i> Atrasado</small>
                                            @endif
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @switch($rental->status)
                                            @case('active')
                                                <span class="badge bg-primary">Activo</span>
                                                @break
                                            @case('returned')
                                                <span class="badge bg-success">Devuelto</span>
                                                @break
                                            @case('overdue')
                                                <span class="badge bg-danger">Atrasado</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ ucfirst($rental->status) }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('rentals.show', $rental) }}" class="btn btn-sm btn-outline-info" title="Ver Detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($rental->status === 'active')
                                                <a href="{{ route('rentals.return-form', $rental) }}" class="btn btn-sm btn-outline-success" title="Procesar Devolución">
                                                    <i class="fas fa-undo"></i>
                                                </a>
                                            @endif
                                            <a href="{{ route('rentals.edit', $rental) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $rentals->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection