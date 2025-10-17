@extends('layouts.app')

@section('title', 'Dashboard de Actividad de Negocio')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="display-6 fw-bold text-gradient">
                        <i class="fas fa-chart-line me-3"></i>Dashboard de Actividad de Negocio
                    </h1>
                    <p class="lead text-muted">Monitoreo en tiempo real de las actividades del sistema</p>
                </div>
                <div>
                    <a href="{{ route('business-activity.index') }}" class="btn btn-outline-primary me-2">
                        <i class="fas fa-list me-2"></i>Ver Todos los Logs
                    </a>
                    <a href="{{ route('audit.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-shield-alt me-2"></i>Auditoría Técnica
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas Generales -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-gradient-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $todayLogs }}</h3>
                            <p class="mb-0">Actividades Hoy</p>
                        </div>
                        <i class="fas fa-calendar-day fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $categoryStats->count() }}</h3>
                            <p class="mb-0">Categorías Activas</p>
                        </div>
                        <i class="fas fa-tags fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $activeUsers->count() }}</h3>
                            <p class="mb-0">Usuarios Activos</p>
                        </div>
                        <i class="fas fa-users fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient-danger text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $criticalActivities->count() }}</h3>
                            <p class="mb-0">Actividades Críticas</p>
                        </div>
                        <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Actividad por Categoría -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Actividad por Categoría (30 días)
                    </h5>
                </div>
                <div class="card-body">
                    @if($categoryStats->count() > 0)
                        @foreach($categoryStats as $stat)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-capitalize fw-medium">
                                        @switch($stat->category)
                                            @case('rental')
                                                <i class="fas fa-film text-primary me-1"></i>Rentas
                                                @break
                                            @case('inventory')
                                                <i class="fas fa-boxes text-success me-1"></i>Inventario
                                                @break
                                            @case('access')
                                                <i class="fas fa-sign-in-alt text-info me-1"></i>Accesos
                                                @break
                                            @case('customer')
                                                <i class="fas fa-users text-warning me-1"></i>Clientes
                                                @break
                                            @case('film')
                                                <i class="fas fa-video text-danger me-1"></i>Películas
                                                @break
                                            @case('staff')
                                                <i class="fas fa-user-tie text-secondary me-1"></i>Personal
                                                @break
                                            @default
                                                <i class="fas fa-circle text-muted me-1"></i>{{ ucfirst($stat->category) }}
                                        @endswitch
                                    </span>
                                    <span class="badge bg-primary">{{ $stat->count }}</span>
                                </div>
                                @php
                                    $percentage = $categoryStats->sum('count') > 0 ? ($stat->count / $categoryStats->sum('count')) * 100 : 0;
                                @endphp
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">No hay actividad en los últimos 30 días</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actividad por Severidad -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">
                        <i class="fas fa-thermometer-half me-2"></i>Severidad de Actividades (7 días)
                    </h5>
                </div>
                <div class="card-body">
                    @if($severityStats->count() > 0)
                        @foreach($severityStats as $stat)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-medium">
                                        @switch($stat->severity)
                                            @case('info')
                                                <i class="fas fa-info-circle text-info me-1"></i>Información
                                                @break
                                            @case('warning')
                                                <i class="fas fa-exclamation-triangle text-warning me-1"></i>Advertencia
                                                @break
                                            @case('high')
                                                <i class="fas fa-exclamation text-danger me-1"></i>Alta
                                                @break
                                            @case('critical')
                                                <i class="fas fa-skull-crossbones text-danger me-1"></i>Crítica
                                                @break
                                        @endswitch
                                    </span>
                                    <span class="badge 
                                        @if($stat->severity === 'info') bg-info
                                        @elseif($stat->severity === 'warning') bg-warning
                                        @elseif($stat->severity === 'high') bg-danger
                                        @else bg-dark
                                        @endif
                                    ">{{ $stat->count }}</span>
                                </div>
                                @php
                                    $percentage = $severityStats->sum('count') > 0 ? ($stat->count / $severityStats->sum('count')) * 100 : 0;
                                    $progressClass = match($stat->severity) {
                                        'info' => 'bg-info',
                                        'warning' => 'bg-warning',
                                        'high' => 'bg-danger',
                                        'critical' => 'bg-dark',
                                        default => 'bg-secondary'
                                    };
                                @endphp
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar {{ $progressClass }}" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">No hay actividad en los últimos 7 días</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Usuarios Más Activos -->
        <div class="col-md-8 mb-4">
            <div class="card h-100">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">
                        <i class="fas fa-trophy me-2"></i>Usuarios Más Activos (30 días)
                    </h5>
                </div>
                <div class="card-body">
                    @if($activeUsers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Usuario</th>
                                        <th>Email</th>
                                        <th>Actividades</th>
                                        <th>Última Actividad</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($activeUsers as $user)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 14px;">
                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                    </div>
                                                    <strong>{{ $user->name }}</strong>
                                                </div>
                                            </td>
                                            <td class="text-muted">{{ $user->email }}</td>
                                            <td>
                                                <span class="badge bg-primary">{{ $user->activity_count }}</span>
                                            </td>
                                            <td class="text-muted">
                                                {{ \Carbon\Carbon::parse($user->last_activity)->diffForHumans() }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No hay actividad de usuarios en los últimos 30 días</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actividades Críticas Recientes -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-transparent">
                    <h5 class="mb-0">
                        <i class="fas fa-bell text-danger me-2"></i>Actividades Críticas
                    </h5>
                </div>
                <div class="card-body">
                    @if($criticalActivities->count() > 0)
                        @foreach($criticalActivities as $activity)
                            <div class="border-bottom pb-2 mb-2">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <span class="badge 
                                            @if($activity->severity === 'high') bg-warning
                                            @else bg-danger
                                            @endif
                                            mb-1
                                        ">{{ ucfirst($activity->severity) }}</span>
                                        <p class="mb-1 small">
                                            <strong>{{ ucfirst($activity->category) }}</strong>: {{ ucfirst($activity->action) }}
                                        </p>
                                        <p class="mb-1 text-muted small">
                                            {{ $activity->user_name ?? 'Sistema' }}
                                        </p>
                                        <p class="mb-0 text-muted" style="font-size: 11px;">
                                            {{ \Carbon\Carbon::parse($activity->created_at)->diffForHumans() }}
                                        </p>
                                    </div>
                                    <a href="{{ route('business-activity.show', $activity->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                        
                        @if($criticalActivities->count() >= 10)
                            <div class="text-center mt-3">
                                <a href="{{ route('business-activity.index', ['severity' => 'high,critical']) }}" class="btn btn-sm btn-outline-danger">
                                    Ver Todas las Críticas
                                </a>
                            </div>
                        @endif
                    @else
                        <p class="text-muted small">No hay actividades críticas recientes</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Actividad Diaria -->
    @if($dailyActivity->count() > 0)
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header bg-transparent">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-line me-2"></i>Actividad Diaria (últimos 14 días)
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($dailyActivity as $day)
                                <div class="col-md-1 text-center mb-3">
                                    <div class="bg-light p-3 rounded">
                                        <div class="fw-bold text-primary">{{ $day->count }}</div>
                                        <div class="small text-muted">
                                            {{ \Carbon\Carbon::parse($day->date)->format('M d') }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
// Auto-refresh cada 30 segundos
setTimeout(function() {
    location.reload();
}, 30000);
</script>
@endpush