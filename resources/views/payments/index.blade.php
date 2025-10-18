@extends('layouts.app')

@section('title', 'Mis Pagos')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="display-5 fw-bold text-primary">
                <i class="fas fa-credit-card me-3"></i>Mis Pagos
            </h1>
            <p class="lead text-muted">Historial de pagos realizados</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('payments.pending') }}" class="btn btn-outline-warning">
                <i class="fas fa-clock me-2"></i>Ver Cargos Pendientes
            </a>
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
            <form method="GET" action="{{ route('payments.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="date_from" class="form-label fw-bold">Fecha Desde</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" 
                               value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to" class="form-label fw-bold">Fecha Hasta</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" 
                               value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="amount_min" class="form-label fw-bold">Monto Mín.</label>
                        <input type="number" class="form-control" id="amount_min" name="amount_min" 
                               value="{{ request('amount_min') }}" step="0.01" min="0">
                    </div>
                    <div class="col-md-2">
                        <label for="amount_max" class="form-label fw-bold">Monto Máx.</label>
                        <input type="number" class="form-control" id="amount_max" name="amount_max" 
                               value="{{ request('amount_max') }}" step="0.01" min="0">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="w-100">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-1"></i>Filtrar
                            </button>
                        </div>
                    </div>
                </div>
                
                @if(request()->hasAny(['date_from', 'date_to', 'amount_min', 'amount_max']))
                    <div class="mt-2">
                        <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times me-1"></i>Limpiar Filtros
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-custom bg-gradient-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="text-white-50 mb-1">Total Pagado</h5>
                            <h3 class="mb-0">${{ number_format($totalAmount, 2) }}</h3>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-custom bg-gradient-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="text-white-50 mb-1">Total de Pagos</h5>
                            <h3 class="mb-0">{{ $totalPayments }}</h3>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-receipt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="card shadow-custom border-0">
        <div class="card-header bg-gradient-dark text-white border-0">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>Historial de Pagos
            </h5>
        </div>
        <div class="card-body p-0">
            @if($payments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Monto</th>
                                <th>Tipo</th>
                                <th>Película</th>
                                <th>Empleado</th>
                                <th>Estado</th>
                                <th>Notas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $payment)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $payment->payment_date->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $payment->payment_date->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-success">{{ $payment->formatted_amount }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $payment->payment_type_label }}</span>
                                    </td>
                                    <td>
                                        @if($payment->rental && $payment->rental->film)
                                            <a href="{{ route('films.show', $payment->rental->film) }}" 
                                               class="text-decoration-none">
                                                {{ $payment->rental->film->title }}
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $payment->staff->first_name }} {{ $payment->staff->last_name }}</div>
                                    </td>
                                    <td>
                                        @php
                                            $statusClass = match($payment->status) {
                                                'Completado' => 'success',
                                                'Pendiente' => 'warning',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $statusClass }}">{{ $payment->status }}</span>
                                    </td>
                                    <td>
                                        @if($payment->notes)
                                            <button type="button" class="btn btn-outline-info btn-sm" 
                                                    data-bs-toggle="tooltip" title="{{ $payment->notes }}">
                                                <i class="fas fa-sticky-note"></i>
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

                <!-- Pagination -->
                <div class="card-footer bg-light border-0">
                    {{ $payments->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-receipt text-muted" style="font-size: 4rem;"></i>
                    <h5 class="text-muted mt-3">No se encontraron pagos</h5>
                    <p class="text-muted">{{ request()->hasAny(['date_from', 'date_to', 'amount_min', 'amount_max']) ? 'Intenta ajustar los filtros de búsqueda.' : 'Aún no has realizado ningún pago.' }}</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.bg-gradient-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
}

.bg-gradient-dark {
    background: linear-gradient(135deg, #343a40 0%, #6c757d 100%);
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #6610f2 100%);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection