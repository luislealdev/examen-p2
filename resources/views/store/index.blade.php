@extends('layouts.app')

@section('title', 'Lista de Tiendas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="text-primary">
        <i class="fas fa-store me-2"></i>Tiendas
    </h1>
    <a href="{{ route('stores.create') }}" class="btn btn-gradient-primary">
        <i class="fas fa-plus me-2"></i>Agregar Nueva Tienda
    </a>
</div>

<div class="card shadow-custom border-0">
    <div class="card-header bg-gradient-primary text-white border-0">
        <h5 class="mb-0">
            <i class="fas fa-list me-2"></i>Lista de Tiendas
        </h5>
    </div>
    <div class="card-body">
        @if($stores->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID de Tienda</th>
                            <th>ID del Gerente</th>
                            <th>ID de Dirección</th>
                            <th>Última Actualización</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stores as $store)
                            <tr>
                                <td>{{ $store->store_id }}</td>
                                <td>{{ $store->manager_staff_id }}</td>
                                <td>{{ $store->address_id }}</td>
                                <td>{{ $store->last_update ? $store->last_update->format('Y-m-d H:i:s') : 'N/A' }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('stores.show', $store->store_id) }}" class="btn btn-sm btn-gradient-info">
                                            <i class="fas fa-eye me-1"></i>Ver
                                        </a>
                                        <a href="{{ route('stores.edit', $store->store_id) }}" class="btn btn-sm btn-gradient-warning">
                                            <i class="fas fa-edit me-1"></i>Editar
                                        </a>
                                        <form action="{{ route('stores.destroy', $store->store_id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta tienda?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash me-1"></i>Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $stores->links() }}
            </div>
        @else
            <div class="text-center py-4">
                <p class="text-muted">No se encontraron tiendas.</p>
                <a href="{{ route('stores.create') }}" class="btn btn-gradient-primary">Agregar la primera tienda</a>
            </div>
        @endif
    </div>
</div>
@endsection