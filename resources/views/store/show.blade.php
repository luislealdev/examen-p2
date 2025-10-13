@extends('layouts.app')

@section('title', 'Detalles de la Tienda')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-custom border-0">
            <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                <h3 class="mb-0">
                    <i class="fas fa-store me-2"></i>Detalles de la Tienda #{{ $store->store_id }}
                </h3>
                <div>
                    <a href="{{ route('stores.edit', $store->store_id) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit me-1"></i>Editar
                    </a>
                    <a href="{{ route('stores.index') }}" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-list me-1"></i>Volver a Lista
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-info-circle me-2"></i>Información General
                        </h5>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>ID de Tienda:</strong></td>
                                <td>{{ $store->store_id }}</td>
                            </tr>
                            <tr>
                                <td><strong>Última Actualización:</strong></td>
                                <td>{{ $store->last_update ? $store->last_update->format('d/m/Y H:i:s') : 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5 class="text-success mb-3">
                            <i class="fas fa-chart-bar me-2"></i>Estadísticas
                        </h5>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Películas en Inventario:</strong></td>
                                <td>
                                    <span class="badge bg-info">{{ $store->inventories->count() }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Manager Information -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-user-tie me-2"></i>Información del Gerente
                                </h5>
                                @if($store->manager)
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="avatar-circle bg-primary text-white me-3">
                                            {{ strtoupper(substr($store->manager->first_name, 0, 1)) }}{{ strtoupper(substr($store->manager->last_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $store->manager->first_name }} {{ $store->manager->last_name }}</h6>
                                            <small class="text-muted">ID: {{ $store->manager->staff_id }}</small>
                                        </div>
                                    </div>
                                    <p class="mb-2">
                                        <i class="fas fa-envelope me-2 text-muted"></i>
                                        <strong>Email:</strong> {{ $store->manager->email }}
                                    </p>
                                    <p class="mb-2">
                                        <i class="fas fa-toggle-{{ $store->manager->active ? 'on text-success' : 'off text-danger' }} me-2"></i>
                                        <strong>Estado:</strong> 
                                        <span class="badge bg-{{ $store->manager->active ? 'success' : 'danger' }}">
                                            {{ $store->manager->active ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </p>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Información del gerente no disponible
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <h5 class="text-success mb-3">
                                    <i class="fas fa-map-marker-alt me-2"></i>Información de Ubicación
                                </h5>
                                @if($store->address)
                                    <div class="mb-3">
                                        <p class="mb-2">
                                            <i class="fas fa-home me-2 text-muted"></i>
                                            <strong>Dirección:</strong><br>
                                            <span class="ms-4">{{ $store->address->address }}</span>
                                            @if($store->address->address2)
                                                <br><span class="ms-4">{{ $store->address->address2 }}</span>
                                            @endif
                                        </p>
                                        @if($store->address->district)
                                            <p class="mb-2">
                                                <i class="fas fa-map me-2 text-muted"></i>
                                                <strong>Distrito:</strong> {{ $store->address->district }}
                                            </p>
                                        @endif
                                        @if($store->address->city)
                                            <p class="mb-2">
                                                <i class="fas fa-city me-2 text-muted"></i>
                                                <strong>Ciudad:</strong> {{ $store->address->city }}
                                            </p>
                                        @endif
                                        @if($store->address->postal_code)
                                            <p class="mb-2">
                                                <i class="fas fa-mail-bulk me-2 text-muted"></i>
                                                <strong>Código Postal:</strong> {{ $store->address->postal_code }}
                                            </p>
                                        @endif
                                        @if($store->address->phone)
                                            <p class="mb-2">
                                                <i class="fas fa-phone me-2 text-muted"></i>
                                                <strong>Teléfono:</strong> {{ $store->address->phone }}
                                            </p>
                                        @endif
                                        <small class="text-muted">
                                            <i class="fas fa-tag me-1"></i>ID de Dirección: {{ $store->address->address_id }}
                                        </small>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Información de dirección no disponible
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Inventory Preview -->
                @if($store->inventories->count() > 0)
                <div class="mt-4">
                    <div class="card border-0">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-film me-2"></i>Inventario de Películas ({{ $store->inventories->count() }} películas)
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($store->inventories->take(6) as $inventory)
                                    <div class="col-md-4 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <i class="fas fa-film text-primary"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $inventory->film->title ?? 'Película no encontrada' }}</h6>
                                                <small class="text-muted">Inventario ID: {{ $inventory->inventory_id }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @if($store->inventories->count() > 6)
                                <div class="text-center mt-3">
                                    <small class="text-muted">... y {{ $store->inventories->count() - 6 }} películas más</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- Actions -->
                <div class="mt-4">
                    <div class="card border-0">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-tools me-2"></i>Acciones
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('stores.edit', $store->store_id) }}" class="btn btn-gradient-warning">
                                    <i class="fas fa-edit me-2"></i>Editar Tienda
                                </a>
                                <form action="{{ route('stores.destroy', $store->store_id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta tienda? Esta acción no se puede deshacer.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash me-2"></i>Eliminar Tienda
                                    </button>
                                </form>
                                <a href="{{ route('stores.index') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-arrow-left me-2"></i>Volver a Lista
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.2em;
}

.shadow-custom {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.btn-gradient-primary {
    background: linear-gradient(45deg, #007bff, #0056b3);
    border: none;
    color: white;
}

.btn-gradient-warning {
    background: linear-gradient(45deg, #ffc107, #e0a800);
    border: none;
    color: #212529;
}

.bg-gradient-primary {
    background: linear-gradient(45deg, #007bff, #0056b3) !important;
}

.card-header {
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}

.text-primary {
    color: #007bff !important;
}

.text-success {
    color: #28a745 !important;
}
</style>
@endsection