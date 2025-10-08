@extends('layouts.app')

@section('title', 'Detalles del Personal')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>Detalles del Personal: {{ $staff->full_name }}</h3>
                <div>
                    <a href="{{ route('staff.edit', $staff->staff_id) }}" class="btn btn-warning btn-sm">Editar</a>
                    <a href="{{ route('staff.index') }}" class="btn btn-secondary btn-sm">Volver a la Lista</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <!-- Profile Picture Section -->
                        <div class="text-center mb-4">
                            @if($staff->picture)
                                <img src="{{ route('staff.picture', $staff->staff_id) }}" 
                                     alt="Foto de Perfil" 
                                     class="rounded-circle mb-3"
                                     style="width: 150px; height: 150px; object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white mx-auto mb-3" 
                                     style="width: 150px; height: 150px; font-size: 3rem;">
                                    {{ $staff->initials }}
                                </div>
                            @endif
                            <h4>{{ $staff->full_name }}</h4>
                            <p class="text-muted">
                                @if($staff->is_manager)
                                    <span class="badge bg-warning">Gerente</span>
                                @else
                                    <span class="badge bg-light text-dark">Personal</span>
                                @endif
                                @if($staff->active)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-secondary">Inactivo</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Información Personal</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>ID de Personal:</strong></td>
                                        <td>{{ $staff->staff_id }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Primer Nombre:</strong></td>
                                        <td>{{ $staff->first_name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Apellido:</strong></td>
                                        <td>{{ $staff->last_name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Correo Electrónico:</strong></td>
                                        <td>{{ $staff->email ?: 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>ID de Dirección:</strong></td>
                                        <td>{{ $staff->address_id }}</td>
                                    </tr>
                                </table>
                            </div>
                            
                            <div class="col-md-6">
                                <h5>Información Laboral</h5>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>ID de Tienda Principal:</strong></td>
                                        <td>{{ $staff->store_id }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Nombre de Usuario:</strong></td>
                                        <td><code>{{ $staff->username }}</code></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Estado:</strong></td>
                                        <td>
                                            @if($staff->active)
                                                <span class="badge bg-success">Activo</span>
                                            @else
                                                <span class="badge bg-secondary">Inactivo</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Última Actualización:</strong></td>
                                        <td>{{ $staff->last_update ? $staff->last_update->format('Y-m-d H:i:s') : 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                @if($staff->store)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Información de Tienda Principal</h5>
                            <div class="alert alert-info">
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>ID de Tienda:</strong> {{ $staff->store->store_id }}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>ID de Personal Gerente:</strong> {{ $staff->store->manager_staff_id }}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>ID de Dirección de Tienda:</strong> {{ $staff->store->address_id }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if($staff->managedStores->count() > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Tiendas Administradas</h5>
                            <div class="alert alert-warning">
                                <p><strong>Este miembro del personal es gerente de las siguientes tiendas:</strong></p>
                                <ul class="mb-0">
                                    @foreach($staff->managedStores as $managedStore)
                                        <li>Tienda {{ $managedStore->store_id }} (ID de Dirección: {{ $managedStore->address_id }})</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- TODO: When Address model is created, uncomment this section
                @if($staff->address)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Información de Dirección</h5>
                            <div class="alert alert-secondary">
                                <strong>Dirección:</strong> {{ $staff->address->address }}<br>
                                <strong>Ciudad:</strong> {{ $staff->address->city }}<br>
                                <strong>Distrito:</strong> {{ $staff->address->district }}<br>
                                <strong>Código Postal:</strong> {{ $staff->address->postal_code }}
                            </div>
                        </div>
                    </div>
                @endif
                --}}

                <div class="mt-4">
                    <h5>Acciones</h5>
                    <div class="btn-group" role="group">
                        <a href="{{ route('staff.edit', $staff->staff_id) }}" class="btn btn-warning">Editar Personal</a>
                        @if($staff->active)
                            <form action="{{ route('staff.destroy', $staff->staff_id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de que desea desactivar a este miembro del personal? Esto no eliminará el registro pero lo marcará como inactivo.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Desactivar Personal</button>
                            </form>
                        @else
                            <span class="text-muted">El miembro del personal ya está inactivo</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection