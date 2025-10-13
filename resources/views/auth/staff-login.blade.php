@extends('layouts.app')

@section('title', 'Acceso Empleados')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-gradient-primary text-white">
                <h4 class="mb-0">
                    <i class="fas fa-user-tie me-2"></i>Acceso Empleados
                </h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('auth.staff.login') }}">
                    @csrf

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Esta página es exclusiva para el personal de Sakila.
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                               id="email" name="email" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                               id="password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Recordar sesión</label>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-gradient-primary">
                            <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection