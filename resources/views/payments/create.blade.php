@extends('layouts.app')

@section('title', 'Agregar Pago Manual')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-plus-circle me-2"></i>Agregar Pago Manual
                </h1>
                <div class="d-flex gap-2">
                    <a href="{{ route('payments.manage') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-list"></i> Ver Todos los Pagos
                    </a>
                    <a href="{{ route('rentals.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left"></i> Volver a Rentas
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-money-bill-wave me-1"></i> Información del Pago</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('payments.store') }}" method="POST" id="paymentForm">
                                @csrf
                                
                                <!-- Selección de Cliente -->
                                <div class="mb-4">
                                    <label for="customer_id" class="form-label fw-bold">Cliente <span class="text-danger">*</span></label>
                                    <select class="form-select @error('customer_id') is-invalid @enderror" 
                                            id="customer_id" name="customer_id" required onchange="loadCustomerRentals()">
                                        <option value="">Seleccionar cliente...</option>
                                        @foreach($customers as $customerOption)
                                            <option value="{{ $customerOption->customer_id }}" 
                                                    {{ old('customer_id', $customer?->customer_id) == $customerOption->customer_id ? 'selected' : '' }}>
                                                {{ $customerOption->first_name }} {{ $customerOption->last_name }} - {{ $customerOption->email }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('customer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <!-- Monto -->
                                        <div class="mb-3">
                                            <label for="amount" class="form-label fw-bold">Monto <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" step="0.01" min="0.01" 
                                                       class="form-control @error('amount') is-invalid @enderror" 
                                                       id="amount" name="amount" value="{{ old('amount') }}" required>
                                            </div>
                                            @error('amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <!-- Tipo de Pago -->
                                        <div class="mb-3">
                                            <label for="payment_type" class="form-label fw-bold">Tipo de Pago <span class="text-danger">*</span></label>
                                            <select class="form-select @error('payment_type') is-invalid @enderror" 
                                                    id="payment_type" name="payment_type" required>
                                                <option value="">Seleccionar tipo...</option>
                                                <option value="rental" {{ old('payment_type') == 'rental' ? 'selected' : '' }}>
                                                    Alquiler
                                                </option>
                                                <option value="late_fee" {{ old('payment_type') == 'late_fee' ? 'selected' : '' }}>
                                                    Multa por Retraso
                                                </option>
                                                <option value="damage" {{ old('payment_type') == 'damage' ? 'selected' : '' }}>
                                                    Daño/Pérdida
                                                </option>
                                                <option value="other" {{ old('payment_type') == 'other' ? 'selected' : '' }}>
                                                    Otro
                                                </option>
                                            </select>
                                            @error('payment_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Renta Asociada (Opcional) -->
                                <div class="mb-3" id="rentalSelection" style="display: none;">
                                    <label for="rental_id" class="form-label fw-bold">Renta Asociada (Opcional)</label>
                                    <select class="form-select @error('rental_id') is-invalid @enderror" id="rental_id" name="rental_id">
                                        <option value="">Sin renta específica</option>
                                        @if($customer && $rentals->isNotEmpty())
                                            @foreach($rentals as $rental)
                                                <option value="{{ $rental->rental_id }}" 
                                                        {{ old('rental_id') == $rental->rental_id ? 'selected' : '' }}>
                                                    #{{ $rental->rental_id }} - {{ $rental->film->title ?? 'Película no encontrada' }} 
                                                    ({{ $rental->rental_date->format('d/m/Y') }})
                                                    @if($rental->return_date)
                                                        - Devuelta
                                                    @else
                                                        - Activa
                                                    @endif
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('rental_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Notas -->
                                <div class="mb-4">
                                    <label for="notes" class="form-label fw-bold">Notas/Descripción</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="3" 
                                              placeholder="Descripción del pago, motivo, etc...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save me-1"></i> Registrar Pago
                                    </button>
                                    <a href="{{ route('payments.manage') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i> Cancelar
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Panel lateral con información -->
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-info-circle me-1"></i> Información</h6>
                        </div>
                        <div class="card-body">
                            <h6>Tipos de Pago:</h6>
                            <ul class="list-unstyled">
                                <li><span class="badge bg-primary me-2">Alquiler</span> Pago por renta de película</li>
                                <li><span class="badge bg-warning me-2">Multa</span> Cargo por retraso en devolución</li>
                                <li><span class="badge bg-danger me-2">Daño</span> Cargo por daño o pérdida</li>
                                <li><span class="badge bg-secondary me-2">Otro</span> Otros tipos de pagos</li>
                            </ul>
                            
                            <hr>
                            
                            <h6>Notas Importantes:</h6>
                            <ul class="small text-muted">
                                <li>Todos los pagos se registran con fecha y hora actual</li>
                                <li>Los pagos manuales quedan registrados a tu nombre como empleado</li>
                                <li>Puedes asociar el pago a una renta específica para mejor trazabilidad</li>
                            </ul>
                        </div>
                    </div>

                    @if($customer)
                        <div class="card shadow-sm mt-3">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-user me-1"></i> Cliente Seleccionado</h6>
                            </div>
                            <div class="card-body">
                                <h6>{{ $customer->first_name }} {{ $customer->last_name }}</h6>
                                <p class="text-muted mb-2">{{ $customer->email }}</p>
                                
                                @if($rentals->isNotEmpty())
                                    <h6 class="mt-3">Rentas Recientes:</h6>
                                    <div class="list-group list-group-flush">
                                        @foreach($rentals->take(5) as $rental)
                                            <div class="list-group-item px-0 py-2 border-0">
                                                <small>
                                                    <strong>#{{ $rental->rental_id }}</strong> - {{ $rental->film->title ?? 'N/A' }}<br>
                                                    <span class="text-muted">{{ $rental->rental_date->format('d/m/Y') }}</span>
                                                    @if($rental->return_date)
                                                        <span class="badge bg-success">Devuelta</span>
                                                    @else
                                                        <span class="badge bg-warning">Activa</span>
                                                    @endif
                                                </small>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function loadCustomerRentals() {
    const customerId = document.getElementById('customer_id').value;
    const rentalSection = document.getElementById('rentalSelection');
    
    if (customerId) {
        rentalSection.style.display = 'block';
        // Reload page with customer_id parameter to load rentals
        const url = new URL(window.location);
        url.searchParams.set('customer_id', customerId);
        window.location.href = url.toString();
    } else {
        rentalSection.style.display = 'none';
    }
}

// Show rental selection if customer is already selected
document.addEventListener('DOMContentLoaded', function() {
    const customerId = document.getElementById('customer_id').value;
    if (customerId) {
        document.getElementById('rentalSelection').style.display = 'block';
    }
});
</script>
@endpush
@endsection