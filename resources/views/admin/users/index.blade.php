@extends('layouts.app')

@section('title', 'Gestión de Usuarios')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="display-5 fw-bold text-primary">
                <i class="fas fa-users me-3"></i>Gestión de Usuarios
            </h1>
            <p class="lead text-muted">Administra empleados y otros administradores del sistema</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.users.create') }}" class="btn btn-gradient-primary btn-lg">
                <i class="fas fa-user-plus me-2"></i>Crear Usuario
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card shadow-custom border-0 mb-4">
        <div class="card-header bg-gradient-info text-white">
            <h6 class="mb-0">
                <i class="fas fa-filter me-2"></i>Filtros
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="search" 
                           placeholder="Buscar por nombre o email..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="role" class="form-select">
                        <option value="">Todos los roles</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administradores</option>
                        <option value="employee" {{ request('role') === 'employee' ? 'selected' : '' }}>Empleados</option>
                        <option value="client" {{ request('role') === 'client' ? 'selected' : '' }}>Clientes</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-search me-1"></i>Filtrar
                    </button>
                    <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de usuarios -->
    <div class="card shadow-custom border-0">
        <div class="card-header bg-gradient-primary text-white">
            <h6 class="mb-0">
                <i class="fas fa-list me-2"></i>Lista de Usuarios ({{ $users->total() }})
            </h6>
        </div>
        <div class="card-body p-0">
            @if($users->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Usuario</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Registro</th>
                                <th width="120">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'employee' ? 'warning' : 'info') }} text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div>
                                                <strong>{{ $user->name }}</strong>
                                                @if($user->id === Auth::id())
                                                    <span class="badge bg-success ms-1">Tú</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'employee' ? 'warning' : 'info') }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $user->created_at->format('d/m/Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.users.edit', $user) }}" 
                                               class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($user->id !== Auth::id())
                                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('¿Estás seguro de eliminar este usuario?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No se encontraron usuarios</h5>
                    <p class="text-muted">Intenta ajustar los filtros de búsqueda</p>
                </div>
            @endif
        </div>
        
        @if($users->hasPages())
            <div class="card-footer bg-light">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection