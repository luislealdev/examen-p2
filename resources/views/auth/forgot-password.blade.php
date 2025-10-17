@extends('layouts.app')

@section('title', 'Recuperar Contraseña')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">
                        <i class="fas fa-key me-2"></i>Recuperar Contraseña
                    </h4>
                </div>
                <div class="card-body p-4">
                    @if (session('status'))
                        <div class="alert alert-success border-0 rounded-3" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle fa-2x me-3 text-success"></i>
                                <div>
                                    <h6 class="mb-1">¡Correo enviado!</h6>
                                    <p class="mb-0">{{ session('status') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="text-center mb-4">
                        <p class="text-muted">
                            Ingresa tu dirección de correo electrónico y te enviaremos un enlace para restablecer tu contraseña.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">
                                <i class="fas fa-envelope me-2 text-muted"></i>Correo Electrónico
                            </label>
                            <input id="email" 
                                   type="email" 
                                   class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autocomplete="email" 
                                   autofocus
                                   placeholder="tu@email.com">

                            @error('email')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane me-2"></i>
                                Enviar Enlace de Recuperación
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

            <!-- Información adicional -->
            <div class="text-center mt-4">
                <div class="card border-0 bg-light">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">
                            <i class="fas fa-info-circle me-2"></i>¿Necesitas ayuda?
                        </h6>
                        <p class="text-muted small mb-0">
                            Si no recibes el correo en unos minutos, verifica tu carpeta de spam o contacta al administrador del sistema.
                        </p>
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
    // Auto focus en el campo email si no hay errores
    const emailInput = document.getElementById('email');
    if (emailInput && !emailInput.classList.contains('is-invalid')) {
        emailInput.focus();
    }

    // Efecto de typing en el placeholder
    const placeholders = [
        'tu@email.com',
        'usuario@empresa.com',
        'nombre@dominio.com'
    ];
    let currentIndex = 0;
    
    setInterval(() => {
        if (emailInput && emailInput.value === '') {
            currentIndex = (currentIndex + 1) % placeholders.length;
            emailInput.placeholder = placeholders[currentIndex];
        }
    }, 3000);
});
</script>
@endsection