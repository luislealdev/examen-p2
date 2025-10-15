@extends('layouts.app')

@section('title', 'Crear Personal')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-custom border-0">
            <div class="card-header bg-gradient-primary text-white">
                <h3 class="mb-0">
                    <i class="fas fa-user-plus me-2"></i>Crear Nuevo Miembro del Personal
                </h3>
            </div>
            <div class="card-body">
                <form action="{{ route('staff.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="text-primary">
                                <i class="fas fa-user me-2"></i>Información Personal
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="first_name" class="form-label fw-bold">Nombre <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control @error('first_name') is-invalid @enderror" 
                                               id="first_name" 
                                               name="first_name" 
                                               value="{{ old('first_name') }}" 
                                               maxlength="45"
                                               placeholder="Ingrese el nombre"
                                               required>
                                        @error('first_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="last_name" class="form-label fw-bold">Apellido <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control @error('last_name') is-invalid @enderror" 
                                               id="last_name" 
                                               name="last_name" 
                                               value="{{ old('last_name') }}" 
                                               maxlength="45"
                                               placeholder="Ingrese el apellido"
                                               required>
                                        @error('last_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label fw-bold">Correo Electrónico</label>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       maxlength="50"
                                       placeholder="correo@ejemplo.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Dirección de correo electrónico del personal (opcional).</div>
                            </div>

                            <div class="mb-3">
                                <label for="address_id" class="form-label fw-bold">ID de Dirección <span class="text-danger">*</span></label>
                                <input type="number" 
                                       class="form-control @error('address_id') is-invalid @enderror" 
                                       id="address_id" 
                                       name="address_id" 
                                       value="{{ old('address_id') }}" 
                                       min="1"
                                       placeholder="ID de dirección"
                                       required>
                                @error('address_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Ingrese el ID de dirección para este miembro del personal.</div>
                            </div>

                            <div class="mb-3">
                                <label for="picture" class="form-label fw-bold">Foto de Perfil</label>
                                <input type="file" 
                                       class="form-control @error('picture') is-invalid @enderror" 
                                       id="picture" 
                                       name="picture" 
                                       accept="image/*">
                                @error('picture')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Opcional. Subir una foto de perfil (máx. 2MB).</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h5 class="text-primary">
                                <i class="fas fa-briefcase me-2"></i>Información Laboral
                            </h5>
                            
                            <div class="mb-3">
                                <label for="store_id" class="form-label fw-bold">Tienda Principal <span class="text-danger">*</span></label>
                                <select class="form-select @error('store_id') is-invalid @enderror" 
                                        id="store_id" 
                                        name="store_id" 
                                        required>
                                    <option value="">Seleccionar una tienda...</option>
                                    @foreach($stores as $store)
                                        <option value="{{ $store->store_id }}" {{ old('store_id') == $store->store_id ? 'selected' : '' }}>
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
                                           {{ old('active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="active">
                                        Empleado Activo
                                    </label>
                                </div>
                                <div class="form-text">Desmarque para crear un empleado inactivo.</div>
                            </div>

                            <div class="mb-3">
                                <label for="role" class="form-label fw-bold">Rol del Usuario <span class="text-danger">*</span></label>
                                <select class="form-select @error('role') is-invalid @enderror" 
                                        id="role" 
                                        name="role" 
                                        required>
                                    <option value="">Seleccionar rol...</option>
                                    <option value="employee" {{ old('role') == 'employee' ? 'selected' : '' }}>Empleado</option>
                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrador</option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Rol para el sistema de autenticación y permisos.</div>
                            </div>

                            <h5 class="mt-4 text-primary">
                                <i class="fas fa-key me-2"></i>Acceso al Sistema
                            </h5>
                            
                            <div class="mb-3">
                                <label for="username" class="form-label fw-bold">Nombre de Usuario <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('username') is-invalid @enderror" 
                                       id="username" 
                                       name="username" 
                                       value="{{ old('username') }}" 
                                       placeholder="Nombre de usuario único"
                                       maxlength="16"
                                       required>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Nombre de usuario para acceso al sistema de alquiler (máx. 16 caracteres).</div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-bold">Contraseña <span class="text-danger">*</span></label>
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       minlength="6"
                                       placeholder="Contraseña segura"
                                       required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Contraseña para acceso al sistema de alquiler (mínimo 6 caracteres).</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('staff.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-gradient-primary">
                            <i class="fas fa-save me-2"></i>Crear Miembro del Personal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection