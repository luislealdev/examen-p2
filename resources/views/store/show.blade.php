@extends('layouts.app')

@section('title', 'Detalles de la Tienda')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>Detalles de la Tienda</h3>
                <div>
                    <a href="{{ route('stores.edit', $store->store_id) }}" class="btn btn-warning btn-sm">Editar</a>
                    <a href="{{ route('stores.index') }}" class="btn btn-secondary btn-sm">Volver a la Lista</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Información de la Tienda</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>ID de la Tienda:</strong></td>
                                <td>{{ $store->store_id }}</td>
                            </tr>
                            <tr>
                                <td><strong>Gerente:</strong></td>
                                <td>
                                    @if($store->manager)
                                        <strong>{{ $store->manager->full_name }}</strong><br>
                                        <small class="text-muted">{{ $store->manager->email ?: $store->manager->username }}</small><br>
                                        <small class="text-muted">
                                            Estado: 
                                            @if($store->manager->active)
                                                <span class="badge bg-success">Activo</span>
                                            @else
                                                <span class="badge bg-secondary">Inactivo</span>
                                            @endif
                                        </small>
                                    @else
                                        <span class="text-muted">ID: {{ $store->manager_staff_id }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Última Actualización:</strong></td>
                                <td>{{ $store->last_update ? $store->last_update->format('d/m/Y H:i:s') : 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <h5>Ubicación y Dirección</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Ciudad:</strong></td>
                                <td>
                                    @if($store->address && $store->address->city)
                                        <strong>{{ $store->address->city->city }}</strong><br>
                                        <small class="text-muted">{{ $store->address->city->country->country ?? 'País desconocido' }}</small>
                                    @else
                                        <span class="text-muted">Sin ciudad asignada</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Dirección:</strong></td>
                                <td>
                                    @if($store->address)
                                        <strong>{{ $store->address->address }}</strong><br>
                                        @if($store->address->address2)
                                            {{ $store->address->address2 }}<br>
                                        @endif
                                        {{ $store->address->district }}<br>
                                        <small class="text-muted">CP: {{ $store->address->postal_code }}</small>
                                        @if($store->address->phone)
                                            <br><small class="text-muted">Tel: {{ $store->address->phone }}</small>
                                        @endif
                                    @else
                                        <span class="text-muted">Dirección ID: {{ $store->address_id }}</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Información Detallada del Gerente -->
                @if($store->manager)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Información Completa del Gerente</h5>
                            <div class="alert alert-info">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Nombre Completo:</strong> {{ $store->manager->full_name }}<br>
                                        <strong>Email:</strong> {{ $store->manager->email ?: 'No especificado' }}<br>
                                        <strong>Usuario:</strong> {{ $store->manager->username }}<br>
                                        <strong>Estado:</strong> 
                                        @if($store->manager->active)
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-secondary">Inactivo</span>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        @if($store->manager->address)
                                            <strong>Dirección del Gerente:</strong><br>
                                            {{ $store->manager->address->address }}<br>
                                            {{ $store->manager->address->district }}, {{ $store->manager->address->city->city ?? 'Ciudad desconocida' }}<br>
                                            {{ $store->manager->address->city->country->country ?? 'País desconocido' }}
                                        @endif
                                        @if($store->manager->picture)
                                            <br><small class="text-muted">Foto de perfil disponible</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Información Completa de la Dirección -->
                @if($store->address)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Información Completa de la Dirección</h5>
                            <div class="alert alert-secondary">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Dirección:</strong> {{ $store->address->address }}<br>
                                        @if($store->address->address2)
                                            <strong>Dirección 2:</strong> {{ $store->address->address2 }}<br>
                                        @endif
                                        <strong>Distrito:</strong> {{ $store->address->district }}<br>
                                        <strong>Código Postal:</strong> {{ $store->address->postal_code }}
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Ciudad:</strong> {{ $store->address->city->city ?? 'Ciudad desconocida' }}<br>
                                        <strong>País:</strong> {{ $store->address->city->country->country ?? 'País desconocido' }}<br>
                                        @if($store->address->phone)
                                            <strong>Teléfono:</strong> {{ $store->address->phone }}<br>
                                        @endif
                                        @if($store->address->coordinates)
                                            <strong>Coordenadas:</strong> {{ $store->address->coordinates['lat'] }}, {{ $store->address->coordinates['lng'] }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="mt-4">
                    <h5>Acciones</h5>
                    <div class="btn-group" role="group">
                        <a href="{{ route('stores.edit', $store->store_id) }}" class="btn btn-warning">Editar Tienda</a>
                        <form action="{{ route('stores.destroy', $store->store_id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta tienda? Esta acción no se puede deshacer.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Eliminar Tienda</button>
                        </form>
                    </div>
                </div>

                {{-- TODO: When Staff and Address models are created, uncomment these sections
                <div class="mt-4">
                    <h5>Manager Information</h5>
                    @if($store->manager)
                        <p><strong>Name:</strong> {{ $store->manager->first_name }} {{ $store->manager->last_name }}</p>
                        <p><strong>Email:</strong> {{ $store->manager->email }}</p>
                    @else
                        <p class="text-muted">Manager information not available</p>
                    @endif
                </div>

                <div class="mt-4">
                    <h5>Address Information</h5>
                    @if($store->address)
                        <p><strong>Address:</strong> {{ $store->address->address }}</p>
                        <p><strong>City:</strong> {{ $store->address->city }}</p>
                        <p><strong>District:</strong> {{ $store->address->district }}</p>
                        <p><strong>Postal Code:</strong> {{ $store->address->postal_code }}</p>
                    @else
                        <p class="text-muted">Address information not available</p>
                    @endif
                </div>
                --}}
            </div>
        </div>
    </div>
</div>
@endsection