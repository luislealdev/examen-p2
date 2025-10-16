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
                                        <td><strong>Tienda Asignada:</strong></td>
                                        <td>
                                            @if($staff->store)
                                                <span class="badge bg-info">Tienda #{{ $staff->store->store_id }}</span>
                                            @else
                                                <span class="badge bg-warning">Sin asignar</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Nombre de Usuario:</strong></td>
                                        <td><code>{{ $staff->username }}</code></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Es Gerente:</strong></td>
                                        <td>
                                            @if($staff->is_manager)
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-crown me-1"></i>Sí
                                                </span>
                                            @else
                                                <span class="badge bg-light text-dark">No</span>
                                            @endif
                                        </td>
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
                            <h5><i class="fas fa-store me-2"></i>Información de Tienda Principal</h5>
                            <div class="card border-info">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="text-info">
                                                <i class="fas fa-building me-2"></i>Datos de la Tienda
                                            </h6>
                                            <table class="table table-sm table-borderless">
                                                <tr>
                                                    <td><strong>ID de Tienda:</strong></td>
                                                    <td><span class="badge bg-info">Tienda #{{ $staff->store->store_id }}</span></td>
                                                </tr>
                                                @if($staff->store->address)
                                                <tr>
                                                    <td><strong>Dirección:</strong></td>
                                                    <td>
                                                        {{ $staff->store->address->address }}
                                                        @if($staff->store->address->address2)
                                                            <br><small class="text-muted">{{ $staff->store->address->address2 }}</small>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Distrito:</strong></td>
                                                    <td>{{ $staff->store->address->district }}</td>
                                                </tr>
                                                @if($staff->store->address->city)
                                                <tr>
                                                    <td><strong>Ciudad/País:</strong></td>
                                                    <td>
                                                        {{ $staff->store->address->city->city }}
                                                        @if($staff->store->address->city->country)
                                                            , {{ $staff->store->address->city->country->country }}
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endif
                                                @if($staff->store->address->postal_code)
                                                <tr>
                                                    <td><strong>Código Postal:</strong></td>
                                                    <td>{{ $staff->store->address->postal_code }}</td>
                                                </tr>
                                                @endif
                                                @endif
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-warning">
                                                <i class="fas fa-user-tie me-2"></i>Gerente de la Tienda
                                            </h6>
                                            @if($staff->store->manager)
                                                <div class="d-flex align-items-center mb-3">
                                                    @if($staff->store->manager->picture)
                                                        <img src="{{ route('staff.picture', $staff->store->manager->staff_id) }}" 
                                                             alt="Foto del Gerente" 
                                                             class="rounded-circle me-3"
                                                             style="width: 50px; height: 50px; object-fit: cover;">
                                                    @else
                                                        <div class="rounded-circle bg-warning d-flex align-items-center justify-content-center text-white me-3" 
                                                             style="width: 50px; height: 50px;">
                                                            {{ strtoupper(substr($staff->store->manager->first_name, 0, 1) . substr($staff->store->manager->last_name, 0, 1)) }}
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <strong>{{ $staff->store->manager->full_name }}</strong>
                                                        <br><small class="text-muted">{{ $staff->store->manager->email }}</small>
                                                        <br><small class="text-muted">Usuario: {{ $staff->store->manager->username }}</small>
                                                    </div>
                                                </div>
                                                @if($staff->staff_id === $staff->store->manager_staff_id)
                                                    <div class="alert alert-success py-2">
                                                        <i class="fas fa-crown me-2"></i>
                                                        <strong>Este empleado ES el gerente de esta tienda</strong>
                                                    </div>
                                                @else
                                                    <a href="{{ route('staff.show', $staff->store->manager->staff_id) }}" 
                                                       class="btn btn-sm btn-outline-warning">
                                                        <i class="fas fa-eye me-1"></i>Ver Detalles del Gerente
                                                    </a>
                                                @endif
                                            @else
                                                <div class="alert alert-warning py-2">
                                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                                    No hay gerente asignado a esta tienda
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if($staff->managedStores->count() > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5><i class="fas fa-crown me-2"></i>Tiendas Administradas</h5>
                            <div class="card border-warning">
                                <div class="card-body">
                                    <p class="mb-3">
                                        <strong>Este miembro del personal es gerente de {{ $staff->managedStores->count() }} tienda(s):</strong>
                                    </p>
                                    
                                    @foreach($staff->managedStores as $managedStore)
                                        <div class="card mb-3 border-light">
                                            <div class="card-body py-3">
                                                <div class="row align-items-center">
                                                    <div class="col-md-2">
                                                        <span class="badge bg-warning fs-6">
                                                            <i class="fas fa-store me-1"></i>
                                                            Tienda #{{ $managedStore->store_id }}
                                                        </span>
                                                    </div>
                                                    <div class="col-md-10">
                                                        @if($managedStore->address)
                                                            <strong>Dirección:</strong> 
                                                            {{ $managedStore->address->address }}
                                                            @if($managedStore->address->address2)
                                                                , {{ $managedStore->address->address2 }}
                                                            @endif
                                                            <br>
                                                            <small class="text-muted">
                                                                {{ $managedStore->address->district }}
                                                                @if($managedStore->address->city)
                                                                    , {{ $managedStore->address->city->city }}
                                                                    @if($managedStore->address->city->country)
                                                                        , {{ $managedStore->address->city->country->country }}
                                                                    @endif
                                                                @endif
                                                                @if($managedStore->address->postal_code)
                                                                    ({{ $managedStore->address->postal_code }})
                                                                @endif
                                                            </small>
                                                        @else
                                                            <em class="text-muted">Dirección no disponible</em>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
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