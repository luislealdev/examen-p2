@extends('layouts.app')

@section('title', 'Lista de Personal')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="text-primary">
        <i class="fas fa-users-cog me-2"></i>Personal
    </h1>
    <div>
        <form method="POST" action="{{ route('staff.sync') }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-gradient-warning me-2" 
                    onclick="return confirm('¿Sincronizar empleados de la tabla Users a Staff?')">
                <i class="fas fa-sync-alt me-2"></i>Sincronizar Empleados
            </button>
        </form>
        <a href="{{ route('staff.create') }}" class="btn btn-gradient-primary">
            <i class="fas fa-plus me-2"></i>Agregar Nuevo Personal
        </a>
    </div>
</div>

<!-- Advanced Search and Filter Section -->
<div class="card shadow-custom border-0 mb-4">
    <div class="card-header bg-gradient-light text-dark border-0">
        <h5 class="mb-0">
            <i class="fas fa-filter me-2"></i>Búsqueda y Filtros Avanzados
        </h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('staff.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="search" class="form-label fw-bold">Buscar</label>
                <input type="text" 
                       class="form-control" 
                       id="search" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Nombre, email o usuario...">
            </div>
            
            <div class="col-md-2">
                <label for="status" class="form-label fw-bold">Estado</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Todo el Personal</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Solo Activos</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Solo Inactivos</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="store_id" class="form-label fw-bold">Tienda</label>
                <select class="form-select" id="store_id" name="store_id">
                    <option value="">Todas las Tiendas</option>
                    @foreach($stores as $store)
                        <option value="{{ $store->store_id }}" {{ request('store_id') == $store->store_id ? 'selected' : '' }}>
                            Tienda {{ $store->store_id }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="is_manager" class="form-label fw-bold">Gerente</label>
                <select class="form-select" id="is_manager" name="is_manager">
                    <option value="">Todo el Personal</option>
                    <option value="yes" {{ request('is_manager') === 'yes' ? 'selected' : '' }}>Solo Gerentes</option>
                    <option value="no" {{ request('is_manager') === 'no' ? 'selected' : '' }}>No Gerentes</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="sort" class="form-label">Ordenar Por</label>
                <select class="form-select" id="sort" name="sort">
                    <option value="last_update" {{ request('sort') === 'last_update' ? 'selected' : '' }}>Última Actualización</option>
                    <option value="first_name" {{ request('sort') === 'first_name' ? 'selected' : '' }}>Nombre</option>
                    <option value="last_name" {{ request('sort') === 'last_name' ? 'selected' : '' }}>Apellido</option>
                    <option value="email" {{ request('sort') === 'email' ? 'selected' : '' }}>Email</option>
                    <option value="username" {{ request('sort') === 'username' ? 'selected' : '' }}>Usuario</option>
                    <option value="store_id" {{ request('sort') === 'store_id' ? 'selected' : '' }}>Tienda</option>
                </select>
            </div>
            
            <div class="col-md-1">
                <label for="direction" class="form-label">Orden</label>
                <select class="form-select" id="direction" name="direction">
                    <option value="desc" {{ request('direction') === 'desc' ? 'selected' : '' }}>↓</option>
                    <option value="asc" {{ request('direction') === 'asc' ? 'selected' : '' }}>↑</option>
                </select>
            </div>
            
            <div class="col-12">
                <button type="submit" class="btn btn-outline-primary">Aplicar Filtros</button>
                <a href="{{ route('staff.index') }}" class="btn btn-outline-secondary">Limpiar</a>
            </div>
        </form>
    </div>
</div>

<!-- Results Section -->
<div class="card">
    <div class="card-body">
        @if($staff->count() > 0)
            <!-- Results summary -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted">
                    Mostrando {{ $staff->firstItem() }} a {{ $staff->lastItem() }} de {{ $staff->total() }} miembros del personal
                </span>
                <span class="text-muted">
                    Página {{ $staff->currentPage() }} de {{ $staff->lastPage() }}
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Foto</th>
                            <th>Nombre</th>
                            <th>Usuario</th>
                            <th>Email</th>
                            <th>Tienda</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($staff as $member)
                            <tr class="{{ !$member['active'] ? 'table-secondary' : '' }}">
                                <td>{{ $member['id'] }}</td>
                                <td>
                                    @if($member['source'] === 'staff_table' && isset($member['picture']))
                                        <img src="{{ route('staff.picture', $member['id']) }}" 
                                             alt="Foto" 
                                             class="rounded-circle"
                                             style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white" 
                                             style="width: 40px; height: 40px;">
                                            {{ strtoupper(substr($member['first_name'], 0, 1) . substr($member['last_name'], 0, 1)) }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $member['full_name'] }}</strong>
                                    @if($member['source'] === 'users_table')
                                        <br><small class="text-muted badge bg-warning">Desde Users</small>
                                    @endif
                                </td>
                                <td>
                                    <code>{{ $member['username'] }}</code>
                                </td>
                                <td>{{ $member['email'] ?: 'N/A' }}</td>
                                <td>
                                    @if($member['store_id'])
                                        <span class="badge bg-info">{{ $member['store_name'] }}</span>
                                    @else
                                        <span class="badge bg-warning">Sin asignar</span>
                                    @endif
                                </td>
                                <td>
                                    @if($member['role'] === 'admin')
                                        <span class="badge bg-danger">
                                            <i class="fas fa-crown me-1"></i>Administrador
                                        </span>
                                    @elseif($member['role'] === 'employee')
                                        <span class="badge bg-primary">
                                            <i class="fas fa-user-tie me-1"></i>Empleado
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-question me-1"></i>{{ ucfirst($member['role']) }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($member['active'])
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-secondary">Inactivo</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @if($member['source'] === 'staff_table')
                                            <a href="{{ route('staff.show', $member['id']) }}" class="btn btn-sm btn-info">Ver</a>
                                            <a href="{{ route('staff.edit', $member['id']) }}" class="btn btn-sm btn-warning">Editar</a>
                                            @if($member['active'])
                                                <form action="{{ route('staff.destroy', $member['id']) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que quieres desactivar este miembro del personal?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Desactivar</button>
                                                </form>
                                            @endif
                                        @else
                                            <span class="text-muted small">Solo en Users</span>
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
                    {{ $staff->links() }}
                </div>
                <div class="text-muted">
                    Total: {{ $staff->total() }} miembros del personal
                </div>
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <p class="text-muted">No se encontraron miembros del personal.</p>
                @if(request()->hasAny(['search', 'status', 'store_id', 'is_manager']))
                    <p class="text-muted">Intenta ajustar tus criterios de búsqueda o filtro.</p>
                    <a href="{{ route('staff.index') }}" class="btn btn-outline-secondary">Limpiar Filtros</a>
                @else
                    <a href="{{ route('staff.create') }}" class="btn btn-gradient-primary">Agregar el primer miembro del personal</a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection