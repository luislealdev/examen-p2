@extends('layouts.app')

@section('title', 'Registrarse')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-custom border-0">
                <div class="card-header bg-gradient-info text-white text-center">
                    <h4 class="mb-0">
                        <i class="fas fa-user-plus me-2"></i>Crear Cuenta
                    </h4>
                    <p class="mb-0 mt-2">Complete todos los datos para registrarse en el sistema</p>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('auth.register') }}" id="registerForm">
                        @csrf
                        
                        <!-- Información Personal -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-0 bg-light">
                                    <div class="card-header bg-transparent border-bottom-0">
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
                                                           placeholder="Tu nombre"
                                                           required 
                                                           autofocus>
                                                    @error('first_name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="last_name" class="form-label fw-bold">
                                                        <i class="fas fa-user me-1"></i>Apellido <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="text" 
                                                           class="form-control @error('last_name') is-invalid @enderror" 
                                                           id="last_name" 
                                                           name="last_name" 
                                                           value="{{ old('last_name') }}" 
                                                           maxlength="45"
                                                           placeholder="Tu apellido"
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
                                            <div class="form-text">Este será tu correo para iniciar sesión</div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="password" class="form-label fw-bold">
                                                        <i class="fas fa-lock me-1"></i>Contraseña <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="position-relative">
                                                        <input type="password" 
                                                               class="form-control @error('password') is-invalid @enderror" 
                                                               id="password" 
                                                               name="password" 
                                                               placeholder="Mínimo 8 caracteres"
                                                               required>
                                                        <button type="button" 
                                                                class="btn btn-outline-secondary position-absolute end-0 top-0 h-100"
                                                                onclick="togglePassword('password')"
                                                                style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                                                            <i class="fas fa-eye" id="password-icon"></i>
                                                        </button>
                                                    </div>
                                                    @error('password')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="password_confirmation" class="form-label fw-bold">
                                                        <i class="fas fa-shield-alt me-1"></i>Confirmar Contraseña <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="position-relative">
                                                        <input type="password" 
                                                               class="form-control" 
                                                               id="password_confirmation" 
                                                               name="password_confirmation" 
                                                               placeholder="Repite tu contraseña"
                                                               required>
                                                        <button type="button" 
                                                                class="btn btn-outline-secondary position-absolute end-0 top-0 h-100"
                                                                onclick="togglePassword('password_confirmation')"
                                                                style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                                                            <i class="fas fa-eye" id="password_confirmation-icon"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dirección -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-0 bg-light">
                                    <div class="card-header bg-transparent border-bottom-0">
                                        <h5 class="mb-0 text-success">
                                            <i class="fas fa-map-marker-alt me-2"></i>Dirección de Contacto
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-8">
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
                                                           placeholder="Calle, número, colonia..."
                                                           required>
                                                    @error('address_line1')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="address_line2" class="form-label fw-bold">
                                                        <i class="fas fa-building me-1"></i>Dirección Secundaria
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
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="district" class="form-label fw-bold">
                                                        <i class="fas fa-map me-1"></i>Distrito/Provincia <span class="text-danger">*</span>
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
                                                        <i class="fas fa-mail-bulk me-1"></i>Código Postal <span class="text-danger">*</span>
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
                                                           placeholder="+1234567890">
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
                                                    <div class="form-text">Selecciona primero un país para cargar las ciudades</div>
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

                    <div class="mt-4">
                        <div class="alert alert-info border-0">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-info-circle fa-2x me-3"></i>
                                <div>
                                    <h6 class="mb-1">Información importante</h6>
                                    <p class="mb-0">Los nuevos registros se crean como clientes. Para obtener acceso de empleado o administrador, contacta al administrador del sistema.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Register form JavaScript loaded');
    
    const countrySelect = document.getElementById('country_id');
    const citySelect = document.getElementById('city_id');
    
    console.log('Country select found:', !!countrySelect);
    console.log('City select found:', !!citySelect);
    
    // Event listener para cargar ciudades cuando se selecciona un país
    if (countrySelect && citySelect) {
        countrySelect.addEventListener('change', function() {
            const countryId = this.value;
            console.log('🌍 País seleccionado:', countryId);
            
            if (!countryId) {
                citySelect.innerHTML = '<option value="">Primero selecciona un país...</option>';
                citySelect.disabled = true;
                return;
            }
            
            // Mostrar carga
            citySelect.innerHTML = '<option value="">Cargando ciudades...</option>';
            citySelect.disabled = true;
            
            console.log('📡 Iniciando petición para obtener ciudades...');
            
            // Petición para obtener ciudades
            fetch(`/cities/by-country?country_id=${countryId}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            })
            .then(response => {
                console.log('📡 Respuesta recibida:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(cities => {
                console.log('🏙️ Ciudades recibidas:', cities);
                
                citySelect.innerHTML = '<option value="">Seleccionar ciudad...</option>';
                
                if (cities && cities.length > 0) {
                    cities.forEach(city => {
                        const option = document.createElement('option');
                        option.value = city.city_id;
                        option.textContent = city.city;
                        citySelect.appendChild(option);
                    });
                    console.log(`✅ ${cities.length} ciudades cargadas correctamente`);
                } else {
                    console.log('⚠️ No se encontraron ciudades para el país seleccionado');
                }
                
                // Habilitar select
                citySelect.disabled = false;
                console.log('✅ Select de ciudades habilitado');
            })
            .catch(error => {
                console.error('❌ Error cargando ciudades:', error);
                citySelect.innerHTML = '<option value="">Error al cargar ciudades</option>';
                citySelect.disabled = false;
            });
        });
    } else {
        console.error('❌ No se encontraron los selects de país o ciudad');
    }

    // Validación de contraseñas coincidentes
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirmation');
    
    if (passwordInput && confirmInput) {
        confirmInput.addEventListener('input', function() {
            const password = passwordInput.value;
            const confirm = this.value;
            
            if (confirm && password !== confirm) {
                this.classList.add('is-invalid');
                if (!this.nextElementSibling || !this.nextElementSibling.classList.contains('invalid-feedback')) {
                    const feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    feedback.innerHTML = '<i class="fas fa-exclamation-circle me-1"></i>Las contraseñas no coinciden';
                    this.parentNode.appendChild(feedback);
                }
            } else {
                this.classList.remove('is-invalid');
                const feedback = this.parentNode.querySelector('.invalid-feedback');
                if (feedback) {
                    feedback.remove();
                }
            }
        });
    }
});

function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(inputId + '-icon');
    
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
</script>
@endsection