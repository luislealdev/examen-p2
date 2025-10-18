@extends('layouts.app')

@section('title', 'Pagos de ' . $customer->first_name . ' ' . $customer->last_name)

@section('content')
<div class="container">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Clientes</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('customers.show', $customer) }}">{{ $customer->first_name }} {{ $customer->last_name }}</a></li>
                    <li class="breadcrumb-item active">Pagos</li>
                </ol>
            </nav>
            <h1 class="display-5 fw-bold text-primary">
                <i class="fas fa-credit-card me-3"></i>Pagos del Cliente
            </h1>
            <p class="lead text-muted">{{ $customer->first_name }} {{ $customer->last_name }} - {{ $customer->email }}</p>
        </div>
        <div class="col-auto">
            <div class="btn-group">
                <a href="{{ route('payments.client-pending', $customer) }}" class="btn btn-outline-warning">
                    <i class="fas fa-clock me-2"></i>Cargos Pendientes
                </a>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#paymentModal">
                    <i class="fas fa-plus me-2"></i>Procesar Pago
                </button>
            </div>
        </div>
    </div>

    <!-- Customer Info Card -->
    <div class="card shadow-custom border-0 mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h6 class="fw-bold mb-2">Información del Cliente</h6>
                    <div class="row">
                        <div class="col-sm-6">
                            <strong>Nombre:</strong> {{ $customer->first_name }} {{ $customer->last_name }}<br>
                            <strong>Email:</strong> {{ $customer->email }}<br>
                            <strong>Cliente desde:</strong> {{ $customer->create_date->format('d/m/Y') }}
                        </div>
                        <div class="col-sm-6">
                            <strong>Estado:</strong> 
                            <span class="badge bg-{{ $customer->active ? 'success' : 'danger' }}">
                                {{ $customer->active ? 'Activo' : 'Inactivo' }}
                            </span><br>
                            <strong>Tienda:</strong> Tienda {{ $customer->store_id }}
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('customers.show', $customer) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-user me-1"></i>Ver Perfil
                        </a>
                        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-edit me-1"></i>Editar
                        </a>
                    </div>
                </div>
            </div>
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
            <form method="GET" action="{{ route('payments.client-payments', $customer) }}">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label for="date_from" class="form-label fw-bold">Fecha Desde</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" 
                               value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
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
                    <div class="col-md-2">
                        <label for="staff_id" class="form-label fw-bold">Empleado</label>
                        <select class="form-select" id="staff_id" name="staff_id">
                            <option value="">Todos</option>
                            @foreach($staff as $employee)
                                <option value="{{ $employee->staff_id }}" 
                                        {{ request('staff_id') == $employee->staff_id ? 'selected' : '' }}>
                                    {{ $employee->first_name }} {{ $employee->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i>Filtrar
                        </button>
                    </div>
                </div>
                
                @if(request()->hasAny(['date_from', 'date_to', 'amount_min', 'amount_max', 'staff_id']))
                    <div class="mt-2">
                        <a href="{{ route('payments.client-payments', $customer) }}" class="btn btn-outline-secondary btn-sm">
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
        <div class="card-header bg-gradient-dark text-white border-0 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>Historial de Pagos
            </h5>
            <button type="button" class="btn btn-light btn-sm" onclick="window.print()">
                <i class="fas fa-print me-1"></i>Imprimir
            </button>
        </div>
        <div class="card-body p-0">
            @if($payments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Fecha</th>
                                <th>Monto</th>
                                <th>Tipo</th>
                                <th>Película</th>
                                <th>Empleado</th>
                                <th>Estado</th>
                                <th>Notas</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $payment)
                                <tr>
                                    <td>
                                        <span class="fw-bold text-primary">#{{ $payment->payment_id }}</span>
                                    </td>
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
                                            <div class="small text-muted">Alquiler #{{ $payment->rental->rental_id }}</div>
                                        @else
                                            <span class="text-muted">Pago manual</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $payment->staff->first_name }} {{ $payment->staff->last_name }}</div>
                                        <small class="text-muted">ID: {{ $payment->staff->staff_id }}</small>
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
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary" 
                                                    data-bs-toggle="tooltip" title="Ver Detalles">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            @if($payment->rental)
                                                <a href="{{ route('rentals.show', $payment->rental) }}" 
                                                   class="btn btn-outline-info"
                                                   data-bs-toggle="tooltip" title="Ver Alquiler">
                                                    <i class="fas fa-film"></i>
                                                </a>
                                            @endif
                                        </div>
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
                    <p class="text-muted">{{ request()->hasAny(['date_from', 'date_to', 'amount_min', 'amount_max', 'staff_id']) ? 'Intenta ajustar los filtros de búsqueda.' : 'Este cliente aún no ha realizado ningún pago.' }}</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Process Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Procesar Pago Manual</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('payments.process', $customer) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="amount" class="form-label fw-bold">Monto *</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" id="amount" name="amount" 
                                   step="0.01" min="0.01" max="999.99" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_type" class="form-label fw-bold">Tipo de Pago *</label>
                        <select class="form-select" id="payment_type" name="payment_type" required>
                            <option value="">Seleccionar tipo</option>
                            <option value="rental">Alquiler</option>
                            <option value="late_fee">Mora</option>
                            <option value="damage">Daño</option>
                            <option value="other">Otro</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="rental_id" class="form-label fw-bold">Alquiler (opcional)</label>
                        <select class="form-select" id="rental_id" name="rental_id">
                            <option value="">No asociar a alquiler específico</option>
                            @foreach($customer->rentals()->whereNull('return_date')->with('film')->get() as $rental)
                                <option value="{{ $rental->rental_id }}">
                                    Alquiler #{{ $rental->rental_id }} - {{ $rental->film->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label fw-bold">Notas</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" 
                                  placeholder="Información adicional sobre el pago..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-credit-card me-2"></i>Procesar Pago
                    </button>
                </div>
            </form>
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

@media print {
    .btn, .modal, .card-header .btn {
        display: none !important;
    }
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