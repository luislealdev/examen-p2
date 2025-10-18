@extends('layouts.app')

@section('title', 'Cargos Pendientes de ' . $customer->first_name . ' ' . $customer->last_name)

@section('content')
<div class="container">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Clientes</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('customers.show', $customer) }}">{{ $customer->first_name }} {{ $customer->last_name }}</a></li>
                    <li class="breadcrumb-item active">Cargos Pendientes</li>
                </ol>
            </nav>
            <h1 class="display-5 fw-bold text-warning">
                <i class="fas fa-clock me-3"></i>Cargos Pendientes del Cliente
            </h1>
            <p class="lead text-muted">{{ $customer->first_name }} {{ $customer->last_name }} - {{ $customer->email }}</p>
        </div>
        <div class="col-auto">
            <div class="btn-group">
                <a href="{{ route('payments.client-payments', $customer) }}" class="btn btn-outline-primary">
                    <i class="fas fa-credit-card me-2"></i>Ver Pagos
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
        <div class="card-header bg-gradient-warning text-white border-0">
            <h5 class="mb-0">
                <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('payments.client-pending', $customer) }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="overdue_only" name="overdue_only" 
                                   value="1" {{ request('overdue_only') ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold text-danger" for="overdue_only">
                                Solo mostrar alquileres vencidos
                            </label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="store_id" class="form-label fw-bold">Tienda</label>
                        <select class="form-select" id="store_id" name="store_id">
                            <option value="">Todas las tiendas</option>
                            @foreach($stores as $store)
                                <option value="{{ $store->store_id }}" 
                                        {{ request('store_id') == $store->store_id ? 'selected' : '' }}>
                                    Tienda {{ $store->store_id }} - {{ $store->address->address }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
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
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-warning w-100">
                            <i class="fas fa-search me-1"></i>Filtrar
                        </button>
                    </div>
                </div>
                
                @if(request()->hasAny(['overdue_only', 'store_id', 'staff_id']))
                    <div class="mt-2">
                        <a href="{{ route('payments.client-pending', $customer) }}" class="btn btn-outline-secondary btn-sm">
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
            <div class="card border-0 shadow-custom bg-gradient-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="text-white-50 mb-1">Total Pendientes</h5>
                            <h3 class="mb-0">{{ $totalPendingCount }}</h3>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-custom bg-gradient-danger text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="text-white-50 mb-1">Alquileres Vencidos</h5>
                            <h3 class="mb-0">{{ $overdueCount }}</h3>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Rentals Table -->
    <div class="card shadow-custom border-0">
        <div class="card-header bg-gradient-dark text-white border-0 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>Alquileres Pendientes
            </h5>
            <div class="btn-group">
                <button type="button" class="btn btn-light btn-sm" onclick="window.print()">
                    <i class="fas fa-print me-1"></i>Imprimir
                </button>
                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#bulkActionModal">
                    <i class="fas fa-tasks me-1"></i>Acciones en Lote
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            @if($pendingRentals->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>
                                    <input type="checkbox" id="select-all" class="form-check-input">
                                </th>
                                <th>ID</th>
                                <th>Película</th>
                                <th>Fecha Alquiler</th>
                                <th>Duración</th>
                                <th>Fecha Límite</th>
                                <th>Estado</th>
                                <th>Tarifa</th>
                                <th>Mora</th>
                                <th>Tienda</th>
                                <th>Empleado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingRentals as $rental)
                                @php
                                    $dueDate = $rental->rental_date->addDays($rental->film->rental_duration);
                                    $isOverdue = $dueDate->isPast();
                                    $daysLate = $isOverdue ? $dueDate->diffInDays(now()) : 0;
                                    $lateFee = $isOverdue ? $daysLate * 1.50 : 0;
                                @endphp
                                <tr class="{{ $isOverdue ? 'table-danger' : '' }}">
                                    <td>
                                        <input type="checkbox" class="form-check-input rental-checkbox" 
                                               value="{{ $rental->rental_id }}">
                                    </td>
                                    <td>
                                        <span class="fw-bold text-primary">#{{ $rental->rental_id }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($rental->film->poster_url)
                                                <img src="{{ $rental->film->poster_url }}" 
                                                     alt="{{ $rental->film->title }}" 
                                                     class="rounded me-2"
                                                     style="width: 40px; height: 60px; object-fit: cover;"
                                                     onerror="this.onerror=null; this.src='/placeholder-movie.svg';">
                                            @else
                                                <img src="/placeholder-movie.svg" 
                                                     alt="Imagen no disponible" 
                                                     class="rounded me-2"
                                                     style="width: 40px; height: 60px; object-fit: cover;">
                                            @endif
                                            <div>
                                                <a href="{{ route('films.show', $rental->film) }}" 
                                                   class="fw-bold text-decoration-none">
                                                    {{ $rental->film->title }}
                                                </a>
                                                <div class="small text-muted">{{ $rental->film->release_year }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $rental->rental_date->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $rental->rental_date->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $rental->film->rental_duration }} días</span>
                                    </td>
                                    <td>
                                        <div class="fw-bold {{ $isOverdue ? 'text-danger' : 'text-warning' }}">
                                            {{ $dueDate->format('d/m/Y') }}
                                        </div>
                                        @if($isOverdue)
                                            <small class="text-danger">{{ $daysLate }} día(s) tarde</small>
                                        @else
                                            <small class="text-muted">{{ $dueDate->diffInDays(now()) }} día(s) restantes</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($isOverdue)
                                            <span class="badge bg-danger">
                                                <i class="fas fa-exclamation-triangle me-1"></i>Vencido
                                            </span>
                                        @else
                                            <span class="badge bg-success">
                                                <i class="fas fa-clock me-1"></i>Activo
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold text-success">
                                            ${{ number_format($rental->film->rental_rate, 2) }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($lateFee > 0)
                                            <div class="fw-bold text-danger">
                                                ${{ number_format($lateFee, 2) }}
                                            </div>
                                        @else
                                            <span class="text-muted">$0.00</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold">Tienda {{ $rental->inventory->store->store_id }}</div>
                                        <small class="text-muted">{{ $rental->inventory->store->address->address }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $rental->staff->first_name }} {{ $rental->staff->last_name }}</div>
                                        <small class="text-muted">ID: {{ $rental->staff->staff_id }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-success"
                                                    data-bs-toggle="modal" data-bs-target="#returnModal{{ $rental->rental_id }}"
                                                    data-bs-toggle="tooltip" title="Procesar Devolución">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                            @if($lateFee > 0)
                                                <button type="button" class="btn btn-outline-warning"
                                                        onclick="processLateFeePayment({{ $rental->rental_id }}, {{ $lateFee }})"
                                                        data-bs-toggle="tooltip" title="Cobrar Mora">
                                                    <i class="fas fa-dollar-sign"></i>
                                                </button>
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
                    {{ $pendingRentals->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                    <h5 class="text-success mt-3">¡Excelente!</h5>
                    <p class="text-muted">{{ request()->hasAny(['overdue_only', 'store_id', 'staff_id']) ? 'No hay alquileres que coincidan con los filtros.' : 'Este cliente no tiene alquileres pendientes.' }}</p>
                </div>
            @endif
        </div>
    </div>

    @if($overdueCount > 0)
        <!-- Warning Notice -->
        <div class="alert alert-warning mt-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-3 fs-4"></i>
                <div>
                    <h6 class="alert-heading mb-1">¡Atención!</h6>
                    <p class="mb-0">Este cliente tiene {{ $overdueCount }} alquiler(es) vencido(s). Considera aplicar cargos por mora y/o suspender el servicio hasta la devolución.</p>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Process Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Procesar Pago de Mora</h5>
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
                            <option value="late_fee">Mora</option>
                            <option value="rental">Alquiler</option>
                            <option value="damage">Daño</option>
                            <option value="other">Otro</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="rental_id" class="form-label fw-bold">Alquiler</label>
                        <select class="form-select" id="rental_id" name="rental_id">
                            <option value="">Seleccionar alquiler</option>
                            @foreach($pendingRentals as $rental)
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
.bg-gradient-warning {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
}

.bg-gradient-danger {
    background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
}

.bg-gradient-dark {
    background: linear-gradient(135deg, #343a40 0%, #6c757d 100%);
}

.table-danger {
    --bs-table-bg: rgba(220, 53, 69, 0.1);
}

@media print {
    .btn, .modal, .card-header .btn-group {
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

    // Select all checkbox functionality
    document.getElementById('select-all').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.rental-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
});

function processLateFeePayment(rentalId, amount) {
    const modal = document.getElementById('paymentModal');
    const form = modal.querySelector('form');
    const amountInput = form.querySelector('#amount');
    const rentalIdInput = form.querySelector('#rental_id');
    const paymentTypeInput = form.querySelector('#payment_type');
    
    amountInput.value = amount.toFixed(2);
    rentalIdInput.value = rentalId;
    paymentTypeInput.value = 'late_fee';
    
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
}
</script>
@endsection