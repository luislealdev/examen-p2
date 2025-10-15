@extends('layouts.app')

@section('title', 'Detalles del Cliente')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>Detalles del Cliente</h3>
                <div>
                    <a href="{{ route('customers.edit', $customer->customer_id) }}" class="btn btn-warning btn-sm">Editar</a>
                    <a href="{{ route('customers.index') }}" class="btn btn-secondary btn-sm">Volver a la Lista</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Información Básica</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>ID del Cliente:</strong></td>
                                <td>{{ $customer->customer_id }}</td>
                            </tr>
                            <tr>
                                <td><strong>Nombre Completo:</strong></td>
                                <td>{{ $customer->full_name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Nombre:</strong></td>
                                <td>{{ $customer->first_name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Apellido:</strong></td>
                                <td>{{ $customer->last_name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>{{ $customer->email ?: 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Estado:</strong></td>
                                <td>
                                    @if($customer->active)
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-secondary">Inactivo</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <h5>Ubicación y Fechas</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Tienda Principal:</strong></td>
                                <td>
                                    @if($customer->store && $customer->store->address)
                                        <strong>{{ $customer->store->address->city->city ?? 'Ciudad desconocida' }}</strong><br>
                                        <small class="text-muted">{{ $customer->store->address->address ?? 'Dirección no disponible' }}</small><br>
                                        <small class="text-muted">Encargado: {{ $customer->store->manager ? $customer->store->manager->full_name : 'No asignado' }}</small>
                                    @else
                                        <span class="text-muted">Tienda ID: {{ $customer->store_id }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Dirección:</strong></td>
                                <td>
                                    @if($customer->address)
                                        <strong>{{ $customer->address->address }}</strong><br>
                                        @if($customer->address->address2)
                                            {{ $customer->address->address2 }}<br>
                                        @endif
                                        {{ $customer->address->district }}, {{ $customer->address->city->city ?? 'Ciudad desconocida' }}<br>
                                        {{ $customer->address->city->country->country ?? 'País desconocido' }}<br>
                                        <small class="text-muted">CP: {{ $customer->address->postal_code }}</small>
                                        @if($customer->address->phone)
                                            <br><small class="text-muted">Tel: {{ $customer->address->phone }}</small>
                                        @endif
                                    @else
                                        <span class="text-muted">Dirección ID: {{ $customer->address_id }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Fecha de Registro:</strong></td>
                                <td>{{ $customer->create_date ? $customer->create_date->format('d/m/Y H:i:s') : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Última Actualización:</strong></td>
                                <td>{{ $customer->last_update ? $customer->last_update->format('d/m/Y H:i:s') : 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($customer->store)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Información de la Tienda Principal</h5>
                            <div class="alert alert-info">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Tienda:</strong> 
                                        @if($customer->store->address)
                                            {{ $customer->store->address->city->city ?? 'Ciudad desconocida' }}
                                        @else
                                            Tienda ID {{ $customer->store->store_id }}
                                        @endif
                                        <br>
                                        <strong>Encargado:</strong> 
                                        @if($customer->store->manager)
                                            {{ $customer->store->manager->full_name }}
                                        @else
                                            ID {{ $customer->store->manager_staff_id }}
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        @if($customer->store->address)
                                            <strong>Dirección de la Tienda:</strong><br>
                                            {{ $customer->store->address->address }}<br>
                                            {{ $customer->store->address->district }}, {{ $customer->store->address->city->city ?? 'Ciudad desconocida' }}<br>
                                            {{ $customer->store->address->city->country->country ?? 'País desconocido' }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if($customer->address)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Información Completa de Dirección</h5>
                            <div class="alert alert-secondary">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Dirección:</strong> {{ $customer->address->address }}<br>
                                        @if($customer->address->address2)
                                            <strong>Dirección 2:</strong> {{ $customer->address->address2 }}<br>
                                        @endif
                                        <strong>Distrito:</strong> {{ $customer->address->district }}<br>
                                        <strong>Código Postal:</strong> {{ $customer->address->postal_code }}
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Ciudad:</strong> {{ $customer->address->city->city ?? 'Ciudad desconocida' }}<br>
                                        <strong>País:</strong> {{ $customer->address->city->country->country ?? 'País desconocido' }}<br>
                                        @if($customer->address->phone)
                                            <strong>Teléfono:</strong> {{ $customer->address->phone }}<br>
                                        @endif
                                        @if($customer->address->coordinates)
                                            <strong>Coordenadas:</strong> {{ $customer->address->coordinates['lat'] }}, {{ $customer->address->coordinates['lng'] }}
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
                        <a href="{{ route('customers.edit', $customer->customer_id) }}" class="btn btn-warning">Editar Cliente</a>
                        @if($customer->active)
                            <form action="{{ route('customers.destroy', $customer->customer_id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que quieres desactivar este cliente? Esto no eliminará el cliente pero lo marcará como inactivo.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Desactivar Cliente</button>
                            </form>
                        @else
                            <span class="text-muted">El cliente ya está inactivo</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection