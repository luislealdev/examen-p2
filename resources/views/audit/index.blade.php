@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-search"></i> Registros de Auditoría
                    </h4>
                    <div>
                        <a href="{{ route('audit.statistics') }}" class="btn btn-light btn-sm me-2">
                            <i class="fas fa-chart-bar"></i> Estadísticas
                        </a>
                        <a href="#" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#cleanupModal">
                            <i class="fas fa-trash"></i> Limpiar Logs Antiguos
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filters Section -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="fas fa-filter"></i> Filtros
                            </h6>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="{{ route('audit.index') }}">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label for="user_id" class="form-label">Usuario:</label>
                                        <select name="user_id" id="user_id" class="form-select">
                                            <option value="">Todos los usuarios</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                    {{ $user->email }} ({{ $user->role }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="action" class="form-label">Acción:</label>
                                        <select name="action" id="action" class="form-select">
                                            <option value="">Todas las acciones</option>
                                            <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>Inicio de sesión</option>
                                            <option value="logout" {{ request('action') == 'logout' ? 'selected' : '' }}>Cierre de sesión</option>
                                            <option value="view" {{ request('action') == 'view' ? 'selected' : '' }}>Visualizar</option>
                                            <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>Crear</option>
                                            <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>Actualizar</option>
                                            <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>Eliminar</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="date_from" class="form-label">Fecha desde:</label>
                                        <input type="date" name="date_from" id="date_from" class="form-control" 
                                               value="{{ request('date_from') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="date_to" class="form-label">Fecha hasta:</label>
                                        <input type="date" name="date_to" id="date_to" class="form-control" 
                                               value="{{ request('date_to') }}">
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-filter"></i> Aplicar Filtros
                                        </button>
                                        <a href="{{ route('audit.index') }}" class="btn btn-secondary ms-2">
                                            <i class="fas fa-times"></i> Limpiar Filtros
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Results Summary -->
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">
                            <i class="fas fa-list"></i> Resultados de Auditoría
                            @if($logs->total() > 0)
                                <span class="badge bg-primary">{{ number_format($logs->total()) }}</span>
                            @endif
                        </h6>
                        @if($logs->hasPages())
                            <small class="text-muted">
                                Mostrando {{ $logs->firstItem() ?? 0 }} - {{ $logs->lastItem() ?? 0 }} de {{ $logs->total() }}
                            </small>
                        @endif
                    </div>

                    <!-- Audit Logs Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Fecha/Hora</th>
                                    <th>Usuario</th>
                                    <th>Acción</th>
                                    <th>Recurso</th>
                                    <th>Método</th>
                                    <th>Dirección IP</th>
                                    <th>Respuesta</th>
                                    <th>Detalles</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                <tr>
                                    <td>
                                        <small>{{ \Carbon\Carbon::parse($log->created_at)->format('j M, Y') }}</small><br>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($log->created_at)->format('H:i:s') }}</small>
                                    </td>
                                    <td>
                                        @if($log->user_email)
                                            <div>
                                                <span class="badge bg-info mb-1">{{ $log->user_role }}</span><br>
                                                <small>{{ $log->user_email }}</small>
                                            </div>
                                        @else
                                            <span class="text-muted">Usuario desconocido</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $log->action == 'login' ? 'success' : ($log->action == 'logout' ? 'warning' : ($log->action == 'delete' ? 'danger' : 'primary')) }}">
                                            @switch($log->action)
                                                @case('login') Inicio sesión @break
                                                @case('logout') Cierre sesión @break
                                                @case('view') Visualizar @break
                                                @case('create') Crear @break
                                                @case('update') Actualizar @break
                                                @case('delete') Eliminar @break
                                                @default {{ ucfirst($log->action) }}
                                            @endswitch
                                        </span>
                                    </td>
                                    <td>
                                        @if($log->resource)
                                            <code class="small">{{ $log->resource }}</code>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $log->method == 'GET' ? 'info' : ($log->method == 'POST' ? 'success' : ($log->method == 'PUT' ? 'warning' : 'danger')) }}">
                                            {{ $log->method }}
                                        </span>
                                    </td>
                                    <td><code class="small">{{ $log->ip_address }}</code></td>
                                    <td>
                                        <span class="badge bg-{{ $log->response_code < 300 ? 'success' : ($log->response_code < 400 ? 'warning' : 'danger') }}">
                                            {{ $log->response_code }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('audit.show', $log->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-search fa-2x mb-2"></i><br>
                                            No se encontraron registros de auditoría
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($logs->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $logs->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cleanup Modal -->
<div class="modal fade" id="cleanupModal" tabindex="-1" aria-labelledby="cleanupModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cleanupModalLabel">
                    <i class="fas fa-trash"></i> Limpiar Registros de Auditoría Antiguos
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form action="{{ route('audit.cleanup') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Advertencia:</strong> Esta acción eliminará permanentemente los registros de auditoría antiguos y no se puede deshacer.
                    </div>
                    <div class="mb-3">
                        <label for="days" class="form-label">Eliminar registros anteriores a:</label>
                        <select name="days" id="days" class="form-select" required>
                            <option value="">Selecciona el período de tiempo</option>
                            <option value="30">30 días</option>
                            <option value="60">60 días</option>
                            <option value="90">90 días</option>
                            <option value="180">6 meses</option>
                            <option value="365">1 año</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Eliminar Registros
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
.table th {
    font-weight: 600;
    border-top: none;
}
.badge {
    font-size: 0.75em;
}
.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}
.card-header {
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}
code.small {
    font-size: 0.8em;
    color: #6c757d;
}
.table-responsive {
    border-radius: 0.375rem;
}
</style>
@endpush