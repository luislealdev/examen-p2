@extends('layouts.app')

@section('title', 'Gestión de Pagos')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-money-bill-wave me-2"></i>Gestión de Pagos
                </h1>
                <div class="d-flex gap-2">
                    <a href="{{ route('payments.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Agregar Pago Manual
                    </a>
                    <a href="{{ route('rentals.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-film"></i> Gestión de Rentas
                    </a>
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-custom bg-gradient-primary text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h5 class="text-white-50 mb-1">Total Pagos</h5>
                                    <h3 class="mb-0">{{ number_format($stats['total_payments']) }}</h3>
                                </div>
                                <div class="fs-1 opacity-50">
                                    <i class="fas fa-receipt"></i>
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
                                    <h5 class="text-white-50 mb-1">Ingresos Totales</h5>
                                    <h3 class="mb-0">${{ number_format($stats['total_amount'], 2) }}</h3>
                                </div>
                                <div class="fs-1 opacity-50">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-custom bg-gradient-info text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <h5 class="text-white-50 mb-1">Pagos Hoy</h5>
                                    <h3 class="mb-0">{{ number_format($stats['today_payments']) }}</h3>
                                </div>
                                <div class="fs-1 opacity-50">
                                    <i class="fas fa-calendar-day"></i>
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
                                    <h5 class="text-white-50 mb-1">Ingresos Hoy</h5>
                                    <h3 class="mb-0">${{ number_format($stats['today_amount'], 2) }}</h3>
                                </div>
                                <div class="fs-1 opacity-50">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="mb-0"><i class="fas fa-list me-1"></i> Lista de Pagos</h5>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#filtersCollapse" aria-expanded="false">
                                <i class="fas fa-filter me-1"></i> Filtros
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="collapse {{ request()->hasAny(['customer_search', 'payment_type', 'date_from', 'date_to']) ? 'show' : '' }}" id="filtersCollapse">
                    <div class="card-body border-bottom">
                        <form method="GET" action="{{ route('payments.manage') }}">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="customer_search" class="form-label fw-bold">Cliente</label>
                                    <input type="text" class="form-control" id="customer_search" name="customer_search" 
                                           value="{{ request('customer_search') }}" placeholder="Nombre o email...">
                                </div>
                                <div class="col-md-2">
                                    <label for="payment_type" class="form-label fw-bold">Tipo</label>
                                    <select class="form-select" id="payment_type" name="payment_type">
                                        <option value="">Todos</option>
                                        <option value="rental" {{ request('payment_type') === 'rental' ? 'selected' : '' }}>Alquiler</option>
                                        <option value="late_fee" {{ request('payment_type') === 'late_fee' ? 'selected' : '' }}>Multa</option>
                                        <option value="damage" {{ request('payment_type') === 'damage' ? 'selected' : '' }}>Daño</option>
                                        <option value="other" {{ request('payment_type') === 'other' ? 'selected' : '' }}>Otro</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="date_from" class="form-label fw-bold">Desde</label>
                                    <input type="date" class="form-control" id="date_from" name="date_from" 
                                           value="{{ request('date_from') }}">
                                </div>
                                <div class="col-md-2">
                                    <label for="date_to" class="form-label fw-bold">Hasta</label>
                                    <input type="date" class="form-control" id="date_to" name="date_to" 
                                           value="{{ request('date_to') }}">
                                </div>
                                <div class="col-md-3 d-flex align-items-end gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-1"></i> Filtrar
                                    </button>
                                    <a href="{{ route('payments.manage') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i> Limpiar
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card-body p-0">
                    @if($payments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Cliente</th>
                                        <th>Tipo</th>
                                        <th>Monto</th>
                                        <th>Renta</th>
                                        <th>Fecha</th>
                                        <th>Empleado</th>
                                        <th>Notas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payments as $payment)
                                        <tr>
                                            <td>
                                                <span class="fw-bold">#{{ $payment->payment_id }}</span>
                                            </td>
                                            <td>
                                                <div>
                                                    <div class="fw-bold">{{ $payment->customer->first_name }} {{ $payment->customer->last_name }}</div>
                                                    <small class="text-muted">{{ $payment->customer->email }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $typeColors = [
                                                        'rental' => 'primary',
                                                        'late_fee' => 'warning',
                                                        'damage' => 'danger',
                                                        'other' => 'secondary'
                                                    ];
                                                    $color = $typeColors[$payment->payment_type] ?? 'secondary';
                                                @endphp
                                                <span class="badge bg-{{ $color }}">
                                                    {{ $payment->payment_type_label }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-success">
                                                    ${{ number_format($payment->amount, 2) }}
                                                </div>
                                            </td>
                                            <td>
                                                @if($payment->rental)
                                                    <div>
                                                        <a href="#" class="text-decoration-none" 
                                                           data-bs-toggle="tooltip" 
                                                           title="{{ $payment->rental->film->title ?? 'Película no encontrada' }}">
                                                            <span class="fw-bold">#{{ $payment->rental->rental_id }}</span>
                                                        </a>
                                                        <small class="text-muted d-block">
                                                            {{ $payment->rental->rental_date->format('d/m/Y') }}
                                                        </small>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Sin renta</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="fw-bold">{{ $payment->payment_date->format('d/m/Y') }}</div>
                                                <small class="text-muted">{{ $payment->payment_date->format('H:i') }}</small>
                                            </td>
                                            <td>
                                                @if($payment->staff)
                                                    <div>
                                                        <div class="fw-bold">{{ $payment->staff->first_name }} {{ $payment->staff->last_name }}</div>
                                                        <small class="text-muted">{{ $payment->staff->email ?? 'Sin email' }}</small>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Sistema</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($payment->notes)
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" 
                                                            data-bs-toggle="popover" 
                                                            data-bs-content="{{ $payment->notes }}"
                                                            data-bs-trigger="hover">
                                                        <i class="fas fa-comment"></i>
                                                    </button>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div class="card-footer">
                            {{ $payments->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No se encontraron pagos</h5>
                            <p class="text-muted">Ajusta los filtros para ver más resultados o <a href="{{ route('payments.create') }}">agrega un pago manual</a>.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
});
</script>
@endpush
@endsection