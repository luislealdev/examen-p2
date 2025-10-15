@extends('layouts.app')

@section('title', 'Panel de Administración')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col">
            <h1 class="display-5 fw-bold text-primary">
                <i class="fas fa-tachometer-alt me-3"></i>Panel de Administración
            </h1>
            <p class="lead text-muted">Bienvenido, {{ Auth::user()->name }}</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white shadow-custom">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['total_users'] }}</h4>
                            <p class="mb-0">Usuarios</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white shadow-custom">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['total_films'] }}</h4>
                            <p class="mb-0">Películas</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-film fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white shadow-custom">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['total_stores'] }}</h4>
                            <p class="mb-0">Tiendas</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-store fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white shadow-custom">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $stats['total_customers'] }}</h4>
                            <p class="mb-0">Clientes</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-friends fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col">
            <div class="card shadow-custom">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>Acciones Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.users') }}" class="btn btn-outline-primary w-100 py-3">
                                <i class="fas fa-users fa-2x d-block mb-2"></i>
                                Gestionar Usuarios
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('films.create') }}" class="btn btn-outline-success w-100 py-3">
                                <i class="fas fa-plus-circle fa-2x d-block mb-2"></i>
                                Agregar Película
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('stores.create') }}" class="btn btn-outline-info w-100 py-3">
                                <i class="fas fa-store-alt fa-2x d-block mb-2"></i>
                                Nueva Tienda
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('films.statistics') }}" class="btn btn-outline-warning w-100 py-3">
                                <i class="fas fa-chart-bar fa-2x d-block mb-2"></i>
                                Ver Estadísticas
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-custom">
                <div class="card-header bg-gradient-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-user-clock me-2"></i>Usuarios Recientes
                    </h6>
                </div>
                <div class="card-body">
                    @if($stats['recent_users']->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($stats['recent_users'] as $user)
                                <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                                    <div>
                                        <strong>{{ $user->name }}</strong><br>
                                        <small class="text-muted">{{ $user->email }}</small>
                                    </div>
                                    <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'employee' ? 'warning' : 'info') }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">No hay usuarios recientes</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-custom">
                <div class="card-header bg-gradient-success text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-film me-2"></i>Películas Recientes
                    </h6>
                </div>
                <div class="card-body">
                    @if($stats['recent_films']->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($stats['recent_films'] as $film)
                                <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                                    <div>
                                        <strong>{{ $film->title }}</strong><br>
                                        <small class="text-muted">{{ $film->release_year ?? 'Sin año' }}</small>
                                    </div>
                                    <span class="badge bg-primary">{{ $film->rating ?? 'Sin rating' }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">No hay películas recientes</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection