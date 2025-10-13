@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Recuperar contraseña</h4>
                </div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Correo electrónico</label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autofocus>
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                            <small class="form-text text-muted">
                                Ingresa el correo electrónico con el que te registraste y te enviaremos un enlace para restablecer tu contraseña.
                            </small>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('login') }}" class="text-decoration-none">
                                <i class="fas fa-arrow-left me-1"></i>Volver al login
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-1"></i>Enviar enlace
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection