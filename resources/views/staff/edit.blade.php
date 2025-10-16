@extends('layouts.app')

@section('title', 'Editar Personal')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header">
                <h3>Editar Personal: {{ $staff->full_name }}</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('staff.update', $staff->staff_id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Información Personal</h5>
                            
                            <!-- Current picture preview -->
                            @if($staff->picture)
                                <div class="mb-3 text-center">
                                    <img src="{{ route('staff.picture', $staff->staff_id) }}" 
                                         alt="Foto Actual" 
                                         class="rounded-circle"
                                         style="width: 100px; height: 100px; object-fit: cover;">
                                    <p class="text-muted small">Foto actual</p>
                                </div>
                            @endif
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="first_name" class="form-label">Nombre <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control @error('first_name') is-invalid @enderror" 
                                               id="first_name" 
                                               name="first_name" 
                                               value="{{ old('first_name', $staff->first_name) }}" 
                                               maxlength="45"
                                               required>
                                        @error('first_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="last_name" class="form-label">Apellido <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control @error('last_name') is-invalid @enderror" 
                                               id="last_name" 
                                               name="last_name" 
                                               value="{{ old('last_name', $staff->last_name) }}" 
                                               maxlength="45"
                                               required>
                                        @error('last_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', $staff->email) }}" 
                                       maxlength="50">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Dirección de correo electrónico opcional del empleado.</div>
                            </div>

                            <div class="mb-3">
                                <label for="address_id" class="form-label">ID de Dirección <span class="text-danger">*</span></label>
                                <input type="number" 
                                       class="form-control @error('address_id') is-invalid @enderror" 
                                       id="address_id" 
                                       name="address_id" 
                                       value="{{ old('address_id', $staff->address_id) }}" 
                                       min="1"
                                       required>
                                @error('address_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Ingrese el ID de dirección para este miembro del personal.</div>
                            </div>

                            <div class="mb-3">
                                <label for="picture" class="form-label">Foto de Perfil</label>
                                <input type="file" 
                                       class="form-control @error('picture') is-invalid @enderror" 
                                       id="picture" 
                                       name="picture" 
                                       accept="image/*">
                                @error('picture')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Opcional. Sube una nueva foto de perfil para reemplazar la actual (máx. 2MB).</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>Información Laboral</h5>
                            
                            <div class="mb-3">
                                <label for="store_id" class="form-label">Tienda Asignada <span class="text-danger">*</span></label>
                                <select class="form-select @error('store_id') is-invalid @enderror" 
                                        id="store_id" 
                                        name="store_id" 
                                        required>
                                    <option value="">Seleccionar una tienda...</option>
                                    @foreach($stores as $store)
                                        <option value="{{ $store->store_id }}" {{ old('store_id', $staff->store_id) == $store->store_id ? 'selected' : '' }}>
                                            Tienda {{ $store->store_id }} (Gerente: {{ $store->manager_staff_id }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('store_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">La tienda donde este miembro del personal está asignado.</div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="active" 
                                           name="active" 
                                           value="1" 
                                           {{ old('active', $staff->active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="active">
                                        Empleado Activo
                                    </label>
                                </div>
                                <div class="form-text">Desmarque para desactivar este empleado.</div>
                            </div>

                            <h5 class="mt-4">Acceso al Sistema</h5>
                            
                            <div class="mb-3">
                                <label for="username" class="form-label">Nombre de Usuario <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('username') is-invalid @enderror" 
                                       id="username" 
                                       name="username" 
                                       value="{{ old('username', $staff->username) }}" 
                                       maxlength="16"
                                       required>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Nombre de usuario para acceso al sistema de rentas (máx. 16 caracteres).</div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       minlength="6">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Deje en blanco para mantener la contraseña actual. Ingrese nueva contraseña para cambiar (mínimo 6 caracteres).</div>
                            </div>

                            <!-- Read-only information -->
                            <div class="bg-light p-3 rounded mt-4">
                                <h6>Información del Sistema (Solo lectura)</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <small><strong>ID del Personal:</strong> {{ $staff->staff_id }}</small>
                                    </div>
                                    <div class="col-md-6">
                                        <small><strong>Última Actualización:</strong> {{ $staff->last_update ? $staff->last_update->format('d/m/Y H:i:s') : 'N/A' }}</small>
                                    </div>
                                </div>
                                @if($staff->is_manager)
                                    <div class="mt-2">
                                        <small><strong>Rol:</strong> <span class="badge bg-warning">Gerente</span></small>
                                        <small class="d-block text-muted">Este miembro del personal administra {{ $staff->managedStores->count() }} tienda(s)</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('staff.show', $staff->staff_id) }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Actualizar Miembro del Personal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection