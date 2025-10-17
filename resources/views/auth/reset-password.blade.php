@extends('layouts.app')

@section('title', 'Restablecer Contraseña')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white text-center">
                    <h4 class="mb-0">
                        <i class="fas fa-lock me-2"></i>Restablecer Contraseña
                    </h4>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="alert alert-info border-0 rounded-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-shield-alt fa-2x me-3 text-info"></i>
                                <div class="text-start">
                                    <h6 class="mb-1">Crear Nueva Contraseña</h6>
                                    <p class="mb-0 small">
                                        Tu nueva contraseña debe tener al menos 8 caracteres para mayor seguridad.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <!-- Token y email ocultos -->
                        <input type="hidden" name="token" value="{{ $token }}">
                        <input type="hidden" name="email" value="{{ $email }}">

                        <!-- Mostrar email del usuario -->
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">
                                <i class="fas fa-user me-2"></i>Cuenta
                            </label>
                            <div class="form-control-plaintext bg-light p-3 rounded">
                                <i class="fas fa-envelope me-2 text-muted"></i>{{ $email }}
                            </div>
                        </div>

                        <!-- Nueva contraseña -->
                        <div class="mb-3">
                            <label for="password" class="form-label fw-bold">
                                <i class="fas fa-key me-2 text-muted"></i>Nueva Contraseña
                            </label>
                            <div class="position-relative">
                                <input id="password" 
                                       type="password" 
                                       class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                       name="password" 
                                       required 
                                       autocomplete="new-password" 
                                       autofocus
                                       placeholder="Ingresa tu nueva contraseña">
                                <button type="button" 
                                        class="btn btn-outline-secondary position-absolute end-0 top-0 h-100"
                                        onclick="togglePassword('password')"
                                        style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                                    <i class="fas fa-eye" id="password-icon"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Mínimo 8 caracteres. Usa una combinación de letras, números y símbolos.
                            </div>
                        </div>

                        <!-- Confirmar contraseña -->
                        <div class="mb-4">
                            <label for="password-confirm" class="form-label fw-bold">
                                <i class="fas fa-shield-alt me-2 text-muted"></i>Confirmar Contraseña
                            </label>
                            <div class="position-relative">
                                <input id="password-confirm" 
                                       type="password" 
                                       class="form-control form-control-lg" 
                                       name="password_confirmation" 
                                       required 
                                       autocomplete="new-password"
                                       placeholder="Confirma tu nueva contraseña">
                                <button type="button" 
                                        class="btn btn-outline-secondary position-absolute end-0 top-0 h-100"
                                        onclick="togglePassword('password-confirm')"
                                        style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                                    <i class="fas fa-eye" id="password-confirm-icon"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Indicador de fortaleza de contraseña -->
                        <div class="mb-4">
                            <div class="password-strength">
                                <div class="form-text mb-2">
                                    <i class="fas fa-chart-line me-1"></i>Fortaleza de la contraseña:
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div id="password-strength-bar" 
                                         class="progress-bar" 
                                         role="progressbar" 
                                         style="width: 0%"
                                         aria-valuenow="0" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>
                                <small id="password-strength-text" class="text-muted"></small>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mb-3">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-check me-2"></i>
                                Restablecer Contraseña
                            </button>
                        </div>

                        <div class="text-center">
                            <a href="{{ route('login') }}" class="text-decoration-none">
                                <i class="fas fa-arrow-left me-2"></i>Volver al Login
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Consejos de seguridad -->
            <div class="mt-4">
                <div class="card border-0 bg-light">
                    <div class="card-body">
                        <h6 class="text-muted mb-3">
                            <i class="fas fa-lightbulb me-2"></i>Consejos para una contraseña segura:
                        </h6>
                        <ul class="list-unstyled small text-muted mb-0">
                            <li class="mb-1">
                                <i class="fas fa-check text-success me-2"></i>
                                Usa al menos 8 caracteres
                            </li>
                            <li class="mb-1">
                                <i class="fas fa-check text-success me-2"></i>
                                Combina mayúsculas y minúsculas
                            </li>
                            <li class="mb-1">
                                <i class="fas fa-check text-success me-2"></i>
                                Incluye números y símbolos
                            </li>
                            <li>
                                <i class="fas fa-check text-success me-2"></i>
                                Evita información personal
                            </li>
                        </ul>
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
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('password-confirm');
    const strengthBar = document.getElementById('password-strength-bar');
    const strengthText = document.getElementById('password-strength-text');

    // Verificar fortaleza de contraseña
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        const strength = calculatePasswordStrength(password);
        updateStrengthIndicator(strength);
    });

    // Verificar coincidencia de contraseñas
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

    function calculatePasswordStrength(password) {
        let score = 0;
        
        if (password.length >= 8) score += 25;
        if (/[a-z]/.test(password)) score += 25;
        if (/[A-Z]/.test(password)) score += 25;
        if (/\d/.test(password)) score += 15;
        if (/[^A-Za-z0-9]/.test(password)) score += 10;
        
        return Math.min(score, 100);
    }

    function updateStrengthIndicator(strength) {
        strengthBar.style.width = strength + '%';
        strengthBar.setAttribute('aria-valuenow', strength);
        
        if (strength < 25) {
            strengthBar.className = 'progress-bar bg-danger';
            strengthText.textContent = 'Muy débil';
            strengthText.className = 'text-danger small';
        } else if (strength < 50) {
            strengthBar.className = 'progress-bar bg-warning';
            strengthText.textContent = 'Débil';
            strengthText.className = 'text-warning small';
        } else if (strength < 75) {
            strengthBar.className = 'progress-bar bg-info';
            strengthText.textContent = 'Buena';
            strengthText.className = 'text-info small';
        } else {
            strengthBar.className = 'progress-bar bg-success';
            strengthText.textContent = 'Excelente';
            strengthText.className = 'text-success small';
        }
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