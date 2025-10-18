@extends('layouts.app')

@section('title', 'Detalles del Cliente')

@section('content')
<div class="container">
    <!-- Encabezado -->
    <div class="row mb-4">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('customers.index') }}">Clientes</a></li>
                    <li class="breadcrumb-item active">{{ $customer->full_name }}</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-6 fw-bold text-gradient">
                        <i class="fas fa-user me-3"></i>{{ $customer->full_name }}
                    </h1>
                    <p class="lead text-muted">Cliente ID: {{ $customer->customer_id }}</p>
                </div>
                <div>
                    @if($stats['is_blocked'])
                        <span class="badge bg-danger fs-6 me-2">
                            <i class="fas fa-ban me-1"></i>BLOQUEADO
                        </span>
                    @endif
                    <div class="btn-group me-2">
                        <a href="{{ route('payments.client-payments', $customer) }}" class="btn btn-success">
                            <i class="fas fa-credit-card me-2"></i>Ver Pagos
                        </a>
                        <a href="{{ route('payments.client-pending', $customer) }}" class="btn btn-warning">
                            <i class="fas fa-clock me-2"></i>Cargos Pendientes
                        </a>
                    </div>
                    <a href="{{ route('customers.edit', $customer->customer_id) }}" class="btn btn-outline-warning">
                        <i class="fas fa-edit me-2"></i>Editar
                    </a>
                    <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerta de Bloqueo -->
    @if($stats['is_blocked'])
        <div class="alert alert-danger border-0 shadow-sm mb-4">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                <div>
                    <h5 class="alert-heading mb-1">Cliente Bloqueado Automáticamente</h5>
                    <p class="mb-0">Este cliente tiene {{ $stats['overdue_rentals'] }} películas en retraso con un cargo total de <strong>${{ number_format($stats['total_late_fees'], 2) }}</strong>. No puede rentar más películas hasta devolver las películas pendientes.</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Estadísticas Rápidas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 bg-primary text-white">
                <div class="card-body text-center">
                    <i class="fas fa-film fa-2x mb-2"></i>
                    <h3 class="mb-0">{{ $stats['total_rentals'] }}</h3>
                    <small>Total de Rentas</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-info text-white">
                <div class="card-body text-center">
                    <i class="fas fa-play-circle fa-2x mb-2"></i>
                    <h3 class="mb-0">{{ $stats['active_rentals'] }}</h3>
                    <small>Rentas Activas</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-{{ $stats['overdue_rentals'] > 0 ? 'danger' : 'success' }} text-white">
                <div class="card-body text-center">
                    <i class="fas fa-clock fa-2x mb-2"></i>
                    <h3 class="mb-0">{{ $stats['overdue_rentals'] }}</h3>
                    <small>En Retraso</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-warning text-dark">
                <div class="card-body text-center">
                    <i class="fas fa-dollar-sign fa-2x mb-2"></i>
                    <h3 class="mb-0">${{ number_format($stats['total_late_fees'], 2) }}</h3>
                    <small>Cargos Pendientes</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Información del Cliente -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user me-2"></i>Información del Cliente
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Estado:</strong>
                        @if($customer->active)
                            <span class="badge bg-success ms-2">Activo</span>
                        @else
                            <span class="badge bg-secondary ms-2">Inactivo</span>
                        @endif
                        @if($stats['is_blocked'])
                            <span class="badge bg-danger ms-1">Bloqueado</span>
                        @endif
                    </div>
                    
                    <div class="mb-3">
                        <strong>Email:</strong><br>
                        <span class="text-muted">{{ $customer->email ?: 'No disponible' }}</span>
                    </div>

                    @if($customer->address)
                        <div class="mb-3">
                            <strong>Dirección:</strong><br>
                            <span class="text-muted">
                                {{ $customer->address->address }}<br>
                                @if($customer->address->address2)
                                    {{ $customer->address->address2 }}<br>
                                @endif
                                {{ $customer->address->district }}<br>
                                {{ $customer->address->city->city ?? 'Ciudad' }}, {{ $customer->address->city->country->country ?? 'País' }}<br>
                                CP: {{ $customer->address->postal_code }}
                                @if($customer->address->phone)
                                    <br>Tel: {{ $customer->address->phone }}
                                @endif
                            </span>
                        </div>
                    @endif

                    @if($customer->store)
                        <div class="mb-3">
                            <strong>Tienda Principal:</strong><br>
                            <span class="text-muted">
                                @if($customer->store->address)
                                    {{ $customer->store->address->city->city ?? 'Ciudad' }}<br>
                                    {{ $customer->store->address->address }}<br>
                                @endif
                                @if($customer->store->manager)
                                    Gerente: {{ $customer->store->manager->full_name }}
                                @endif
                            </span>
                        </div>
                    @endif

                    <div class="mb-3">
                        <strong>Registrado:</strong><br>
                        <span class="text-muted">{{ $customer->create_date ? $customer->create_date->format('d/m/Y H:i') : 'N/A' }}</span>
                    </div>

                    <div class="mb-0">
                        <strong>Cargos Totales de por Vida:</strong><br>
                        <span class="text-danger fw-bold">${{ number_format($stats['lifetime_late_fees'], 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rentas Activas -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-gradient-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-play-circle me-2"></i>Rentas Activas
                        <span class="badge bg-white text-dark ms-2">{{ count($activeRentals) }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    @if(count($activeRentals) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Película</th>
                                        <th>Fecha de Renta</th>
                                        <th>Días Restantes</th>
                                        <th>Cargo por Retraso</th>
                                        <th>Estado</th>
                                        @if(auth()->user()->isStaff())
                                            <th>Acciones</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($activeRentals as $rental)
                                        @php
                                            $daysRemaining = 7 - \Carbon\Carbon::parse($rental->rental_date)->diffInDays(now());
                                            $isOverdue = $daysRemaining < 0;
                                            $lateFee = $isOverdue ? abs($daysRemaining) * 1.50 : 0;
                                        @endphp
                                        <tr class="{{ $isOverdue ? 'table-danger' : ($daysRemaining <= 1 ? 'table-warning' : '') }}">
                                            <td>
                                                <strong>{{ $rental->inventory->film->title }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $rental->inventory->film->release_year }}</small>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($rental->rental_date)->format('d/m/Y') }}</td>
                                            <td>
                                                @if($isOverdue)
                                                    <span class="text-danger fw-bold">
                                                        {{ abs($daysRemaining) }} días de retraso
                                                    </span>
                                                @elseif($daysRemaining <= 1)
                                                    <span class="text-warning fw-bold">
                                                        Vence {{ $daysRemaining == 0 ? 'hoy' : 'mañana' }}
                                                    </span>
                                                @else
                                                    <span class="text-success">
                                                        {{ $daysRemaining }} días restantes
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($lateFee > 0)
                                                    <span class="text-danger fw-bold">${{ number_format($lateFee, 2) }}</span>
                                                @else
                                                    <span class="text-muted">$0.00</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($isOverdue)
                                                    <span class="badge bg-danger">En Retraso</span>
                                                @elseif($daysRemaining <= 1)
                                                    <span class="badge bg-warning text-dark">Por Vencer</span>
                                                @else
                                                    <span class="badge bg-success">A Tiempo</span>
                                                @endif
                                            </td>
                                            @if(auth()->user()->isStaff())
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-success" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#returnModalCustomer{{ $rental->rental_id }}">
                                                        <i class="fas fa-undo"></i> Devolver
                                                    </button>
                                                </td>
                                            @endif
                                        </tr>
                                        
                                        @if(auth()->user()->isStaff())
                                            <!-- Modal para procesar devolución desde perfil del cliente -->
                                            <div class="modal fade" id="returnModalCustomer{{ $rental->rental_id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form method="POST" action="{{ route('rentals.process-return', $rental) }}">
                                                            @csrf
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Procesar Devolución</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="alert alert-info">
                                                                    <strong>Cliente:</strong> {{ $customer->first_name }} {{ $customer->last_name }}<br>
                                                                    <strong>Película:</strong> {{ $rental->inventory->film->title }}<br>
                                                                    <strong>Rentada:</strong> {{ \Carbon\Carbon::parse($rental->rental_date)->format('d/m/Y H:i') }}<br>
                                                                    <strong>Días transcurridos:</strong> {{ \Carbon\Carbon::parse($rental->rental_date)->diffInDays(now()) }} días
                                                                    @if($isOverdue)
                                                                        <br><strong class="text-danger">Cargo por retraso:</strong> ${{ number_format($lateFee, 2) }}
                                                                    @endif
                                                                </div>
                                                                
                                                                <div class="mb-3">
                                                                    <label for="conditionCustomer{{ $rental->rental_id }}" class="form-label">Estado de la película</label>
                                                                    <select name="condition" id="conditionCustomer{{ $rental->rental_id }}" class="form-select" required>
                                                                        <option value="available">Buenas condiciones (disponible para renta)</option>
                                                                        <option value="damaged">Dañada (no disponible hasta reparación)</option>
                                                                        <option value="lost">Perdida (no se devolvió)</option>
                                                                    </select>
                                                                </div>
                                                                
                                                                <div class="mb-3">
                                                                    <label for="notesCustomer{{ $rental->rental_id }}" class="form-label">Notas (opcional)</label>
                                                                    <textarea name="notes" id="notesCustomer{{ $rental->rental_id }}" class="form-control" rows="3" 
                                                                              placeholder="Describe cualquier daño o situación especial..."></textarea>
                                                                </div>

                                                                @if($isOverdue)
                                                                    <div class="alert alert-warning">
                                                                        <i class="fas fa-exclamation-triangle"></i>
                                                                        <strong>Película con retraso:</strong> Se aplicará un cargo de ${{ number_format($lateFee, 2) }} por {{ abs($daysRemaining) }} días de retraso.
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                                <button type="submit" class="btn btn-success">
                                                                    <i class="fas fa-check"></i> Procesar Devolución
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">No hay rentas activas</h6>
                            <p class="text-muted">Este cliente no tiene películas rentadas actualmente.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Historial de Rentas -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i>Historial de Rentas
                        <span class="badge bg-white text-dark ms-2">{{ $stats['total_rentals'] }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    @if(count($rentalHistory) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Película</th>
                                        <th>Fecha de Renta</th>
                                        <th>Fecha de Devolución</th>
                                        <th>Días de Retraso</th>
                                        <th>Cargo por Retraso</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rentalHistory as $rental)
                                        @php
                                            $dueDate = \Carbon\Carbon::parse($rental->rental_date)->addDays(7);
                                            $returnDate = $rental->return_date ? \Carbon\Carbon::parse($rental->return_date) : now();
                                            $daysLate = $returnDate->gt($dueDate) ? $returnDate->diffInDays($dueDate) : 0;
                                            $lateFee = $daysLate * 1.50;
                                        @endphp
                                        <tr>
                                            <td>
                                                <strong>{{ $rental->inventory->film->title }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $rental->inventory->film->release_year }}</small>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($rental->rental_date)->format('d/m/Y') }}</td>
                                            <td>
                                                @if($rental->return_date)
                                                    {{ \Carbon\Carbon::parse($rental->return_date)->format('d/m/Y') }}
                                                @else
                                                    <span class="badge bg-warning text-dark">No devuelta</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($daysLate > 0)
                                                    <span class="text-danger fw-bold">{{ $daysLate }} días</span>
                                                @else
                                                    <span class="text-success">A tiempo</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($lateFee > 0)
                                                    <span class="text-danger fw-bold">${{ number_format($lateFee, 2) }}</span>
                                                @else
                                                    <span class="text-muted">$0.00</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if(!$rental->return_date)
                                                    @if($daysLate > 0)
                                                        <span class="badge bg-danger">En Retraso</span>
                                                    @else
                                                        <span class="badge bg-info">Activa</span>
                                                    @endif
                                                @else
                                                    @if($daysLate > 0)
                                                        <span class="badge bg-warning text-dark">Devuelta con Retraso</span>
                                                    @else
                                                        <span class="badge bg-success">Devuelta a Tiempo</span>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @if($stats['total_rentals'] > 20)
                            <div class="text-center mt-3">
                                <small class="text-muted">
                                    Mostrando las últimas 20 rentas de {{ $stats['total_rentals'] }} totales
                                </small>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">No hay historial de rentas</h6>
                            <p class="text-muted">Este cliente aún no ha rentado películas.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Botones de Acción -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="d-flex gap-2">
                <a href="{{ route('customers.edit', $customer) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i>Editar Cliente
                </a>
                <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Volver a Clientes
                </a>
                @if($stats['is_blocked'])
                    <button class="btn btn-warning" onclick="alert('Funcionalidad de desbloqueo en desarrollo')">
                        <i class="fas fa-unlock me-1"></i>Desbloquear Cliente
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh para cargos en tiempo real (cada 5 minutos)
    setTimeout(function() {
        location.reload();
    }, 300000);
    
    // Tooltip para información adicional
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection