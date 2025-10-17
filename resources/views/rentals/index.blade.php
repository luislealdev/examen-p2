@extends('layouts.app')

@section('title', 'Gestión de Rentas')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Gestión de Rentas</h1>
                <div class="d-flex gap-2">
                    <a href="{{ route('films.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-plus"></i> Nueva Renta
                    </a>
                    <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-boxes"></i> Inventario
                    </a>
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4>{{ $stats['active'] }}</h4>
                                    <p class="mb-0">Rentas Activas</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-film fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4>{{ $stats['overdue'] }}</h4>
                                    <p class="mb-0">Vencidas</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-exclamation-triangle fa-2x"></i>
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
                                    <h4>{{ $stats['today'] }}</h4>
                                    <p class="mb-0">Hoy</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-calendar-day fa-2x"></i>
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
                                    <h4>{{ $stats['this_week'] }}</h4>
                                    <p class="mb-0">Esta Semana</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-calendar-week fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('rentals.index') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="status" class="form-label">Estado</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">Todas</option>
                                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Activas</option>
                                    <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>Devueltas</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="search" class="form-label">Buscar</label>
                                <input type="text" name="search" id="search" class="form-control" 
                                       placeholder="Cliente, película, ID..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="overdue" class="form-label">Filtros</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="overdue" value="1" 
                                           id="overdue" {{ request('overdue') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="overdue">
                                        Solo vencidas
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <div class="btn-group w-100">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Filtrar
                                    </button>
                                    <a href="{{ route('rentals.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> Limpiar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Lista de Rentas -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        Lista de Rentas 
                        <span class="badge bg-secondary">{{ $rentals->total() }} total</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($rentals->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Cliente</th>
                                        <th>Película</th>
                                        <th>Fecha Renta</th>
                                        <th>Días</th>
                                        <th>Estado</th>
                                        <th>Tienda</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rentals as $rental)
                                        @php
                                            $daysRented = now()->diffInDays($rental->rental_date);
                                            $isOverdue = $daysRented > 7 && !$rental->isReturned();
                                        @endphp
                                        <tr class="{{ $isOverdue ? 'table-warning' : '' }}">
                                            <td>
                                                <strong>#{{ $rental->rental_id }}</strong>
                                                @if($isOverdue)
                                                    <br><small class="text-danger">
                                                        <i class="fas fa-exclamation-triangle"></i> Vencida
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $rental->customer->first_name }} {{ $rental->customer->last_name }}</strong>
                                                <br><small class="text-muted">{{ $rental->customer->email }}</small>
                                                <br><a href="{{ route('customers.show', $rental->customer) }}" 
                                                       class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-user"></i> Ver Perfil
                                                </a>
                                            </td>
                                            <td>
                                                <strong>{{ $rental->inventory->film->title }}</strong>
                                                <br><small class="text-muted">
                                                    {{ $rental->inventory->film->release_year }} • 
                                                    {{ $rental->inventory->film->rating }}
                                                </small>
                                                @if($rental->inventory->film->category)
                                                    <br><span class="badge bg-info">{{ $rental->inventory->film->category->name }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $rental->rental_date->format('d/m/Y H:i') }}
                                                <br><small class="text-muted">{{ $rental->rental_date->diffForHumans() }}</small>
                                            </td>
                                            <td>
                                                <span class="badge {{ $isOverdue ? 'bg-danger' : ($daysRented > 5 ? 'bg-warning' : 'bg-success') }}">
                                                    {{ $daysRented }} día{{ $daysRented != 1 ? 's' : '' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($rental->isReturned())
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check"></i> Devuelta
                                                    </span>
                                                    <br><small class="text-muted">{{ $rental->return_date->format('d/m/Y') }}</small>
                                                @else
                                                    <span class="badge bg-primary">
                                                        <i class="fas fa-clock"></i> Activa
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($rental->staff && $rental->staff->store)
                                                    <small class="text-muted">Tienda #{{ $rental->staff->store->store_id }}</small>
                                                @else
                                                    <small class="text-muted">N/A</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if(!$rental->isReturned())
                                                    <button type="button" class="btn btn-sm btn-success" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#returnModal{{ $rental->rental_id }}">
                                                        <i class="fas fa-undo"></i> Procesar Devolución
                                                    </button>
                                                    
                                                    <!-- Modal para procesar devolución -->
                                                    <div class="modal fade" id="returnModal{{ $rental->rental_id }}" tabindex="-1">
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
                                                                            <strong>Cliente:</strong> {{ $rental->customer->first_name }} {{ $rental->customer->last_name }}<br>
                                                                            <strong>Película:</strong> {{ $rental->inventory->film->title }}<br>
                                                                            <strong>Rentada:</strong> {{ $rental->rental_date->format('d/m/Y H:i') }} ({{ $daysRented }} días)
                                                                        </div>
                                                                        
                                                                        <div class="mb-3">
                                                                            <label for="condition{{ $rental->rental_id }}" class="form-label">Estado de la película</label>
                                                                            <select name="condition" id="condition{{ $rental->rental_id }}" class="form-select" required>
                                                                                <option value="available">Buenas condiciones (disponible para renta)</option>
                                                                                <option value="damaged">Dañada (no disponible hasta reparación)</option>
                                                                                <option value="lost">Perdida (no se devolvió)</option>
                                                                            </select>
                                                                        </div>
                                                                        
                                                                        <div class="mb-3">
                                                                            <label for="notes{{ $rental->rental_id }}" class="form-label">Notas (opcional)</label>
                                                                            <textarea name="notes" id="notes{{ $rental->rental_id }}" class="form-control" rows="3" 
                                                                                      placeholder="Describe cualquier daño o situación especial..."></textarea>
                                                                        </div>
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
                                                @else
                                                    <small class="text-muted">
                                                        Devuelta el {{ $rental->return_date->format('d/m/Y') }}
                                                    </small>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div class="card-footer">
                            {{ $rentals->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No se encontraron rentas</h5>
                            <p class="text-muted">Ajusta los filtros para ver más resultados.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh cada 30 segundos para rentas activas
    @if(!request()->filled('status') || request('status') === 'active')
        setTimeout(function() {
            window.location.reload();
        }, 30000);
    @endif
    
    // Cambio de estado en modal
    document.querySelectorAll('select[name="condition"]').forEach(function(select) {
        select.addEventListener('change', function() {
            const notesField = this.closest('.modal-body').querySelector('textarea[name="notes"]');
            if (this.value === 'available') {
                notesField.placeholder = 'Notas opcionales sobre la devolución...';
                notesField.required = false;
            } else {
                notesField.placeholder = 'Describe el daño o la situación (requerido)...';
                notesField.required = true;
            }
        });
    });
});
</script>
@endpush