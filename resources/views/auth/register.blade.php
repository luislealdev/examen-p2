@extends('layouts.app')

@section('title', 'Registro de Cliente')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-gradient-info text-white py-4">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">
                                <i class="fas fa-user-plus me-2"></i>Registro de Cliente
                            </h3>
                            <p class="mb-0 opacity-75">Crea tu cuenta para acceder al sistema</p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-5">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <div>
                                    <strong>Error en el formulario:</strong>
                                    <ul class="mb-0 mt-2">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="row">
                            <!-- Información Personal -->
                            <div class="col-md-6">
                                <div class="card h-100 border-primary border-opacity-25">
                                    <div class="card-header bg-primary bg-opacity-10">
                                        <h5 class="mb-0 text-primary">
                                            <i class="fas fa-user me-2"></i>Información Personal
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="first_name" class="form-label fw-bold">
                                                        <i class="fas fa-user me-1"></i>Nombre <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" 
                                                           class="form-control @error('first_name') is-invalid @enderror" 
                                                           id="first_name" 
                                                           name="first_name" 
                                                           value="{{ old('first_name') }}" 
                                                           maxlength="45"
                                                           placeholder="Tu nombre..."
                                                           required>
                                                    @error('first_name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="last_name" class="form-label fw-bold">
                                                        <i class="fas fa-user me-1"></i>Apellidos <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" 
                                                           class="form-control @error('last_name') is-invalid @enderror" 
                                                           id="last_name" 
                                                           name="last_name" 
                                                           value="{{ old('last_name') }}" 
                                                           maxlength="45"
                                                           placeholder="Tus apellidos..."
                                                           required>
                                                    @error('last_name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="email" class="form-label fw-bold">
                                                <i class="fas fa-envelope me-1"></i>Correo Electrónico <span class="text-danger">*</span>
                                            </label>
                                            <input type="email" 
                                                   class="form-control @error('email') is-invalid @enderror" 
                                                   id="email" 
                                                   name="email" 
                                                   value="{{ old('email') }}" 
                                                   maxlength="50"
                                                   placeholder="tu@email.com"
                                                   required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="password" class="form-label fw-bold">
                                                        <i class="fas fa-lock me-1"></i>Contraseña <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="input-group">
                                                        <input type="password" 
                                                               class="form-control @error('password') is-invalid @enderror" 
                                                               id="password" 
                                                               name="password" 
                                                               minlength="8"
                                                               placeholder="Mínimo 8 caracteres"
                                                               required>
                                                        <button class="btn btn-outline-secondary" 
                                                                type="button" 
                                                                onclick="togglePassword('password')">
                                                            <i class="fas fa-eye" id="password-icon"></i>
                                                        </button>
                                                        @error('password')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="password_confirmation" class="form-label fw-bold">
                                                        <i class="fas fa-lock me-1"></i>Confirmar Contraseña <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="input-group">
                                                        <input type="password" 
                                                               class="form-control @error('password_confirmation') is-invalid @enderror" 
                                                               id="password_confirmation" 
                                                               name="password_confirmation" 
                                                               minlength="8"
                                                               placeholder="Repetir contraseña"
                                                               required>
                                                        <button class="btn btn-outline-secondary" 
                                                                type="button" 
                                                                onclick="togglePassword('password_confirmation')">
                                                            <i class="fas fa-eye" id="password_confirmation-icon"></i>
                                                        </button>
                                                        @error('password_confirmation')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Información de Dirección -->
                            <div class="col-md-6">
                                <div class="card h-100 border-success border-opacity-25">
                                    <div class="card-header bg-success bg-opacity-10">
                                        <h5 class="mb-0 text-success">
                                            <i class="fas fa-map-marker-alt me-2"></i>Información de Dirección
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="address_line1" class="form-label fw-bold">
                                                <i class="fas fa-home me-1"></i>Dirección Principal <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" 
                                                   class="form-control @error('address_line1') is-invalid @enderror" 
                                                   id="address_line1" 
                                                   name="address_line1" 
                                                   value="{{ old('address_line1') }}" 
                                                   maxlength="50"
                                                   placeholder="Calle, número..."
                                                   required>
                                            @error('address_line1')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="address_line2" class="form-label fw-bold">
                                                <i class="fas fa-building me-1"></i>Dirección Adicional
                                            </label>
                                            <input type="text" 
                                                   class="form-control @error('address_line2') is-invalid @enderror" 
                                                   id="address_line2" 
                                                   name="address_line2" 
                                                   value="{{ old('address_line2') }}" 
                                                   maxlength="50"
                                                   placeholder="Apartamento, suite...">
                                            @error('address_line2')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="district" class="form-label fw-bold">
                                                        <i class="fas fa-map me-1"></i>Distrito <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" 
                                                           class="form-control @error('district') is-invalid @enderror" 
                                                           id="district" 
                                                           name="district" 
                                                           value="{{ old('district') }}" 
                                                           maxlength="20"
                                                           placeholder="Distrito..."
                                                           required>
                                                    @error('district')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="postal_code" class="form-label fw-bold">
                                                        <i class="fas fa-mail-bulk me-1"></i>C.P. <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" 
                                                           class="form-control @error('postal_code') is-invalid @enderror" 
                                                           id="postal_code" 
                                                           name="postal_code" 
                                                           value="{{ old('postal_code') }}" 
                                                           maxlength="10"
                                                           placeholder="12345"
                                                           required>
                                                    @error('postal_code')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="phone" class="form-label fw-bold">
                                                        <i class="fas fa-phone me-1"></i>Teléfono
                                                    </label>
                                                    <input type="text" 
                                                           class="form-control @error('phone') is-invalid @enderror" 
                                                           id="phone" 
                                                           name="phone" 
                                                           value="{{ old('phone') }}" 
                                                           maxlength="20"
                                                           placeholder="+123456">
                                                    @error('phone')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="country_id" class="form-label fw-bold">
                                                        <i class="fas fa-globe me-1"></i>País <span class="text-danger">*</span>
                                                    </label>
                                                    <select class="form-select @error('country_id') is-invalid @enderror" 
                                                            id="country_id" 
                                                            name="country_id" 
                                                            required>
                                                        <option value="">Seleccionar país...</option>
                                                        @foreach(\App\Models\Country::orderBy('country')->get() as $country)
                                                            <option value="{{ $country->country_id }}" {{ old('country_id') == $country->country_id ? 'selected' : '' }}>
                                                                {{ $country->country }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('country_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="city_id" class="form-label fw-bold">
                                                        <i class="fas fa-city me-1"></i>Ciudad <span class="text-danger">*</span>
                                                    </label>
                                                    <select class="form-select @error('city_id') is-invalid @enderror" 
                                                            id="city_id" 
                                                            name="city_id" 
                                                            required
                                                            disabled>
                                                        <option value="">Primero selecciona un país...</option>
                                                    </select>
                                                    @error('city_id')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <div class="form-text">Selecciona primero un país</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-arrow-left me-2"></i>Volver al Login
                            </a>
                            <button type="submit" class="btn btn-gradient-info btn-lg">
                                <i class="fas fa-user-plus me-2"></i>Crear Cuenta
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
console.log("🔥 JavaScript cargando...");

document.addEventListener('DOMContentLoaded', function() {
    console.log("🚀 DOMContentLoaded ejecutado");
    
    const countrySelect = document.getElementById('country_id');
    const citySelect = document.getElementById('city_id');
    
    console.log("🔍 Elementos:", {
        countrySelect: countrySelect,
        citySelect: citySelect
    });
    
    if (!countrySelect || !citySelect) {
        console.error('❌ Error: No se encontraron los elementos select');
        return;
    }
    
    console.log("✅ Elementos encontrados, agregando event listener");
    
    countrySelect.addEventListener('change', function() {
        console.log("🌍 Change event disparado, valor:", this.value);
        
        const countryId = this.value;
        
        if (!countryId) {
            citySelect.innerHTML = '<option value="">Primero selecciona un país...</option>';
            citySelect.disabled = true;
            console.log("🔒 Select deshabilitado");
            return;
        }
        
        console.log("📡 Iniciando fetch para país:", countryId);
        
        // Mostrar estado de carga
        citySelect.innerHTML = '<option value="">Cargando ciudades...</option>';
        citySelect.disabled = true;
        
        // Hacer petición AJAX
        fetch(`/cities/by-country?country_id=${countryId}`)
        .then(response => {
            console.log("📥 Respuesta recibida:", response.status);
            return response.json();
        })
        .then(cities => {
            console.log("🏙️ Ciudades recibidas:", cities);
            
            // Limpiar select
            citySelect.innerHTML = '<option value="">Seleccionar ciudad...</option>';
            
            // Agregar ciudades
            if (cities && cities.length > 0) {
                cities.forEach(city => {
                    const option = document.createElement('option');
                    option.value = city.city_id;
                    option.textContent = city.city;
                    citySelect.appendChild(option);
                });
                console.log(`✅ ${cities.length} ciudades agregadas`);
            }
            
            // Habilitar select
            citySelect.disabled = false;
            console.log("🔓 Select habilitado");
        })
        .catch(error => {
            console.error('❌ Error en fetch:', error);
            citySelect.innerHTML = '<option value="">Error al cargar ciudades</option>';
            citySelect.disabled = false;
        });
    });
    
    console.log("🎉 Inicialización completa");
});

console.log("📄 Script cargado completamente");

function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(inputId + '-icon');
    
    if (input && icon) {
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
}
</script>
@endpush