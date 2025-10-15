@extends('layouts.app')

@section('title', 'Devolver Película')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-primary">
                <i class="fas fa-undo me-2"></i>Devolver Película
            </h1>
            <p class="text-muted">Completa la devolución de tu película alquilada</p>
        </div>
        <a href="{{ route('rentals.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Volver a Mis Alquileres
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Por favor corrige los siguientes errores:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-check-circle me-2"></i>Información de Devolución
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('rentals.process-return', $rental) }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="return_condition" class="form-label">
                                <i class="fas fa-clipboard-check me-1"></i>¿En qué condición devuelves la película? *
                            </label>
                            <select class="form-select @error('return_condition') is-invalid @enderror" 
                                    id="return_condition" name="return_condition" required>
                                <option value="">Selecciona la condición...</option>
                                <option value="excellent" {{ old('return_condition') == 'excellent' ? 'selected' : '' }}>
                                    ✨ Excelente - Sin daños
                                </option>
                                <option value="good" {{ old('return_condition') == 'good' ? 'selected' : '' }}>
                                    👍 Buena - Uso normal
                                </option>
                                <option value="fair" {{ old('return_condition') == 'fair' ? 'selected' : '' }}>
                                    ⚠️ Regular - Uso evidente pero funcional
                                </option>
                                <option value="damaged" {{ old('return_condition') == 'damaged' ? 'selected' : '' }}>
                                    🔧 Dañada - Requiere reparación
                                </option>
                            </select>
                            @error('return_condition')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Sé honesto sobre la condición. Esto nos ayuda a mantener un buen servicio para todos.
                            </div>
                        </div>

                        @if($currentLateFee > 0)
                            <div class="alert alert-warning">
                                <i class="fas fa-clock me-2"></i>
                                <strong>Cargo por retraso:</strong> 
                                Esta película debía devolverse el {{ $rental->due_date->format('d/m/Y') }}.
                                Se aplicará un cargo de <strong>${{ number_format($currentLateFee, 2) }}</strong>.
                            </div>
                        @endif

                        <div class="mb-4">
                            <label for="notes" class="form-label">
                                <i class="fas fa-comment me-1"></i>Comentarios adicionales (opcional)
                            </label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3" 
                                      placeholder="¿Hay algo que quieras comentar sobre la película o el alquiler?">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-gradient-primary">
                                <i class="fas fa-check me-1"></i>Confirmar Devolución
                            </button>
                            <a href="{{ route('rentals.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Información de la película -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-film me-1"></i>Película a Devolver
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ $rental->inventory->film->poster_url ?? asset('images/default-poster.jpg') }}" 
                             alt="Poster" class="me-3 rounded" style="width: 60px; height: 90px; object-fit: cover;">
                        <div>
                            <h6 class="text-primary mb-1">{{ $rental->inventory->film->title }}</h6>
                            <small class="text-muted">{{ $rental->inventory->film->release_year }}</small>
                        </div>
                    </div>
                    
                    <div class="row g-2">
                        <div class="col-6">
                            <small class="text-muted d-block">Alquilado</small>
                            <strong>{{ $rental->rental_date->format('d/m/Y') }}</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Fecha límite</small>
                            <strong class="{{ $rental->is_overdue ? 'text-danger' : 'text-success' }}">
                                {{ $rental->due_date->format('d/m/Y') }}
                            </strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Tienda</small>
                            <strong>Store #{{ $rental->inventory->store->store_id }}</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Costo</small>
                            <strong class="text-success">${{ $rental->rental_amount }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información del proceso -->
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-1"></i>¿Qué pasa después?
                    </h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <p class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Tu devolución será procesada inmediatamente
                        </p>
                        <p class="mb-2">
                            <i class="fas fa-clock text-info me-2"></i>
                            Si hay cargos adicionales, se notificarán por email
                        </p>
                        <p class="mb-0">
                            <i class="fas fa-star text-warning me-2"></i>
                            ¡Gracias por alquilar con nosotros!
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection