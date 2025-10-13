@extends('layouts.app')

@section('title', 'Lista de Clientes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="text-primary">
        <i class="fas fa-users me-2"></i>Clientes
    </h1>
    <a href="{{ route('customers.create') }}" class="btn btn-gradient-primary">
        <i class="fas fa-plus me-2"></i>Agregar Nuevo Cliente
    </a>
</div>

<!-- Search and Filter Section -->
<div class="card shadow-custom border-0 mb-4">
    <div class="card-header bg-gradient-light text-dark border-0">
        <h5 class="mb-0">
            <i class="fas fa-filter me-2"></i>Buscar y Filtrar
        </h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('customers.index') }}" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label fw-bold">Buscar</label>
                <input type="text" 
                       class="form-control" 
                       id="search" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Buscar por nombre o email...">
            </div>
            
            <div class="col-md-3">
                <label for="status" class="form-label fw-bold">Estado</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Todos los Clientes</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Solo Activos</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Solo Inactivos</option>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="sort" class="form-label fw-bold">Ordenar Por</label>
                <select class="form-select" id="sort" name="sort">
                    <option value="last_update" {{ request('sort') === 'last_update' ? 'selected' : '' }}>Última Actualización</option>
                    <option value="first_name" {{ request('sort') === 'first_name' ? 'selected' : '' }}>Nombre</option>
                    <option value="last_name" {{ request('sort') === 'last_name' ? 'selected' : '' }}>Apellido</option>
                    <option value="email" {{ request('sort') === 'email' ? 'selected' : '' }}>Email</option>
                    <option value="create_date" {{ request('sort') === 'create_date' ? 'selected' : '' }}>Fecha de Creación</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="direction" class="form-label fw-bold">Orden</label>
                <select class="form-select" id="direction" name="direction">
                    <option value="desc" {{ request('direction') === 'desc' ? 'selected' : '' }}>Descendente</option>
                    <option value="asc" {{ request('direction') === 'asc' ? 'selected' : '' }}>Ascendente</option>
                </select>
            </div>
            
            <div class="col-12">
                <button type="submit" class="btn btn-outline-primary">Aplicar Filtros</button>
                <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">Limpiar</a>
            </div>
        </form>
    </div>
</div>

<!-- Results Section -->
<div class="card">
    <div class="card-body">
        @if($customers->count() > 0)
            <!-- Results summary -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted">
                    Mostrando {{ $customers->firstItem() }} a {{ $customers->lastItem() }} de {{ $customers->total() }} clientes
                </span>
                <span class="text-muted">
                    Página {{ $customers->currentPage() }} de {{ $customers->lastPage() }}
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>ID de Tienda</th>
                            <th>ID de Dirección</th>
                            <th>Estado</th>
                            <th>Creado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $customer)
                            <tr class="{{ !$customer->active ? 'table-secondary' : '' }}">
                                <td>{{ $customer->customer_id }}</td>
                                <td>
                                    <strong>{{ $customer->full_name }}</strong>
                                </td>
                                <td>{{ $customer->email ?: 'N/A' }}</td>
                                <td>{{ $customer->store_id }}</td>
                                <td>{{ $customer->address_id }}</td>
                                <td>
                                    @if($customer->active)
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-secondary">Inactivo</span>
                                    @endif
                                </td>
                                <td>{{ $customer->create_date ? $customer->create_date->format('Y-m-d') : 'N/A' }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('customers.show', $customer->customer_id) }}" class="btn btn-sm btn-info">Ver</a>
                                        <a href="{{ route('customers.edit', $customer->customer_id) }}" class="btn btn-sm btn-warning">Editar</a>
                                        @if($customer->active)
                                            <form action="{{ route('customers.destroy', $customer->customer_id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que quieres desactivar este cliente?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">Desactivar</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Enhanced Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div>
                    {{ $customers->links() }}
                </div>
                <div class="text-muted">
                    Total: {{ $customers->total() }} clientes
                </div>
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <p class="text-muted">No se encontraron clientes.</p>
                @if(request()->hasAny(['search', 'status']))
                    <p class="text-muted">Intenta ajustar tus criterios de búsqueda o filtro.</p>
                    <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">Limpiar Filtros</a>
                @else
                    <a href="{{ route('customers.create') }}" class="btn btn-gradient-primary">Agregar el primer cliente</a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection