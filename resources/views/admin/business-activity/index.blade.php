@extends('layouts.app')

@section('title', 'Logs de Actividad de Negocio')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="display-6 fw-bold text-gradient">
                        <i class="fas fa-clipboard-list me-3"></i>Logs de Actividad de Negocio
                    </h1>
                    <p class="lead text-muted">Registro detallado de todas las actividades de negocio del sistema</p>
                </div>
                <div>
                    <a href="{{ route('business-activity.dashboard') }}" class="btn btn-gradient-primary">
                        <i class="fas fa-chart-line me-2"></i>Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros Avanzados -->
    <div class="card mb-4">
        <div class="card-header bg-transparent">
            <h5 class="mb-0">
                <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-2">
                    <label for="category" class="form-label">Categoría</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">Todas las categorías</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                                {{ ucfirst($category) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="action" class="form-label">Acción</label>
                    <select class="form-select" id="action" name="action">
                        <option value="">Todas las acciones</option>
                        @foreach($actions as $action)
                            <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>
                                {{ ucfirst($action) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="severity" class="form-label">Severidad</label>
                    <select class="form-select" id="severity" name="severity">
                        <option value="">Todas las severidades</option>
                        <option value="info" {{ request('severity') === 'info' ? 'selected' : '' }}>Información</option>
                        <option value="warning" {{ request('severity') === 'warning' ? 'selected' : '' }}>Advertencia</option>
                        <option value="high" {{ request('severity') === 'high' ? 'selected' : '' }}>Alta</option>
                        <option value="critical" {{ request('severity') === 'critical' ? 'selected' : '' }}>Crítica</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="user_id" class="form-label">Usuario</label>
                    <select class="form-select" id="user_id" name="user_id">
                        <option value="">Todos los usuarios</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="date_from" class="form-label">Desde</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                </div>

                <div class="col-md-2">
                    <label for="date_to" class="form-label">Hasta</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                </div>

                <div class="col-md-8">
                    <label for="search" class="form-label">Búsqueda libre</label>
                    <input type="text" 
                           class="form-control" 
                           id="search" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Buscar en usuarios, clientes, películas, detalles...">
                </div>

                <div class="col-md-2">
                    <label for="sort" class="form-label">Ordenar por</label>
                    <select class="form-select" id="sort" name="sort">
                        <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Fecha</option>
                        <option value="category" {{ request('sort') === 'category' ? 'selected' : '' }}>Categoría</option>
                        <option value="action" {{ request('sort') === 'action' ? 'selected' : '' }}>Acción</option>
                        <option value="severity" {{ request('sort') === 'severity' ? 'selected' : '' }}>Severidad</option>
                        <option value="user_name" {{ request('sort') === 'user_name' ? 'selected' : '' }}>Usuario</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="direction" class="form-label">Dirección</label>
                    <select class="form-select" id="direction" name="direction">
                        <option value="desc" {{ request('direction') === 'desc' ? 'selected' : '' }}>Descendente</option>
                        <option value="asc" {{ request('direction') === 'asc' ? 'selected' : '' }}>Ascendente</option>
                    </select>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Buscar
                    </button>
                    <a href="{{ route('business-activity.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Resultados -->
    <div class="card">
        <div class="card-header bg-transparent">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    Logs de Actividad 
                    <span class="badge bg-primary">{{ $logs->total() }}</span>
                </h5>
                <div class="text-muted">
                    Mostrando {{ $logs->firstItem() }} - {{ $logs->lastItem() }} de {{ $logs->total() }} resultados
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($logs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha/Hora</th>
                                <th>Categoría</th>
                                <th>Acción</th>
                                <th>Usuario</th>
                                <th>Entidad</th>
                                <th>Severidad</th>
                                <th>IP</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                                <tr>
                                    <td>
                                        <div class="text-nowrap">
                                            <div class="fw-medium">{{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y') }}</div>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($log->created_at)->format('H:i:s') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge 
                                            @switch($log->category)
                                                @case('rental') bg-primary @break
                                                @case('inventory') bg-success @break
                                                @case('access') bg-info @break
                                                @case('customer') bg-warning @break
                                                @case('film') bg-danger @break
                                                @case('staff') bg-secondary @break
                                                @default bg-dark
                                            @endswitch
                                        ">
                                            {{ ucfirst($log->category) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-capitalize">{{ $log->action }}</span>
                                    </td>
                                    <td>
                                        @if($log->user_name)
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 24px; height: 24px; font-size: 10px;">
                                                    {{ strtoupper(substr($log->user_name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-medium">{{ $log->user_name }}</div>
                                                    <small class="text-muted">{{ $log->user_email }}</small>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">Sistema</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="text-capitalize">
                                            {{ $log->entity_type }}
                                            @if($log->entity_id)
                                                <span class="text-muted">#{{ $log->entity_id }}</span>
                                            @endif
                                        </div>
                                        @if($log->customer_name)
                                            <small class="text-muted">Cliente: {{ $log->customer_name }}</small>
                                        @elseif($log->film_title)
                                            <small class="text-muted">Película: {{ Str::limit($log->film_title, 20) }}</small>
                                        @elseif($log->staff_name)
                                            <small class="text-muted">Staff: {{ $log->staff_name }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge 
                                            @switch($log->severity)
                                                @case('info') bg-info @break
                                                @case('warning') bg-warning @break
                                                @case('high') bg-danger @break
                                                @case('critical') bg-dark @break
                                                @default bg-secondary
                                            @endswitch
                                        ">
                                            {{ ucfirst($log->severity) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="font-monospace text-muted">{{ $log->ip_address }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('business-activity.show', $log->id) }}" class="btn btn-sm btn-outline-primary" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="card-footer bg-transparent">
                    {{ $logs->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No se encontraron logs de actividad</h5>
                    <p class="text-muted">Intenta ajustar los filtros de búsqueda</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-completar fechas por defecto
document.addEventListener('DOMContentLoaded', function() {
    const dateFrom = document.getElementById('date_from');
    const dateTo = document.getElementById('date_to');
    
    if (!dateFrom.value && !dateTo.value) {
        // Por defecto, mostrar últimos 7 días
        const today = new Date();
        const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
        
        dateFrom.value = weekAgo.toISOString().split('T')[0];
        dateTo.value = today.toISOString().split('T')[0];
    }
});
</script>
@endpush