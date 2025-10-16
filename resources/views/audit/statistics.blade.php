@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Estadísticas de Auditoría</h4>
                    <a href="{{ route('audit.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver a los Registros
                    </a>
                </div>

                <div class="card-body">
                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Total de Registros</h6>
                                            <h4>{{ number_format($stats['total_logs']) }}</h4>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-list fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Usuarios Activos</h6>
                                            <h4>{{ $stats['active_users'] }}</h4>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-users fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Registros de Hoy</h6>
                                            <h4>{{ $stats['today_logs'] }}</h4>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-calendar-day fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Peticiones Fallidas</h6>
                                            <h4>{{ $stats['failed_requests'] }}</h4>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Actions by Type -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Acciones por Tipo</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="actionsChart" width="400" height="200"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Top Users -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Usuarios más Activos</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Usuario</th>
                                                    <th>Rol</th>
                                                    <th>Acciones</th>
                                                    <th>Última Actividad</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($stats['top_users'] as $user)
                                                <tr>
                                                    <td>{{ $user->user_email ?: 'Desconocido' }}</td>
                                                    <td>
                                                        <span class="badge bg-info">{{ $user->user_role ?: 'N/A' }}</span>
                                                    </td>
                                                    <td>{{ $user->action_count }}</td>
                                                    <td>{{ $user->last_activity ? \Carbon\Carbon::parse($user->last_activity)->diffForHumans() : 'N/A' }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                    <div class="row mt-4">
                        <!-- Recent Activity -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Actividad Reciente (Últimos 7 Días)</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="activityChart" width="400" height="200"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- HTTP Methods -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Distribución de Métodos HTTP</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="methodsChart" width="400" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <!-- Response Codes -->
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Distribución de Códigos de Respuesta</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Código de Respuesta</th>
                                                    <th>Cantidad</th>
                                                    <th>Porcentaje</th>
                                                    <th>Estado</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($stats['response_codes'] as $code)
                                                <tr>
                                                    <td>{{ $code->response_code }}</td>
                                                    <td>{{ $code->count }}</td>
                                                    <td>{{ number_format(($code->count / $stats['total_logs']) * 100, 2) }}%</td>
                                                    <td>
                                                        <span class="badge bg-{{ $code->response_code < 300 ? 'success' : ($code->response_code < 400 ? 'warning' : 'danger') }}">
                                                            @if($code->response_code < 300)
                                                                Éxito
                                                            @elseif($code->response_code < 400)
                                                                Redirección
                                                            @else
                                                                Error
                                                            @endif
                                                        </span>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Actions Chart
    const actionsData = @json($stats['actions_by_type']);
    const actionsChart = new Chart(document.getElementById('actionsChart'), {
        type: 'doughnut',
        data: {
            labels: actionsData.map(item => item.action.charAt(0).toUpperCase() + item.action.slice(1)),
            datasets: [{
                data: actionsData.map(item => item.count),
                backgroundColor: [
                    '#FF6384',
                    '#36A2EB',
                    '#FFCE56',
                    '#4BC0C0',
                    '#9966FF',
                    '#FF9F40'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Activity Chart
    const activityData = @json($stats['daily_activity']);
    const activityChart = new Chart(document.getElementById('activityChart'), {
        type: 'line',
        data: {
            labels: activityData.map(item => item.date),
            datasets: [{
                label: 'Actividad Diaria',
                data: activityData.map(item => item.count),
                borderColor: '#36A2EB',
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Methods Chart
    const methodsData = @json($stats['methods']);
    const methodsChart = new Chart(document.getElementById('methodsChart'), {
        type: 'bar',
        data: {
            labels: methodsData.map(item => item.method),
            datasets: [{
                label: 'Peticiones',
                data: methodsData.map(item => item.count),
                backgroundColor: [
                    '#36A2EB',
                    '#4BC0C0',
                    '#FFCE56',
                    '#FF6384'
                ]
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
@endpush