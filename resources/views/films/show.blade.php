@extends('layouts.app')

@section('title', $film->title)

@section('content')
<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('films.index') }}">Películas</a></li>
            <li class="breadcrumb-item active">{{ $film->title }}</li>
        </ol>
    </nav>

    <!-- Film Header -->
    <div class="row align-items-center mb-4">
        <div class="col">
            <div class="d-flex align-items-center mb-2">
                <h1 class="display-5 fw-bold text-primary me-3">{{ $film->title }}</h1>
                <span class="badge bg-{{ $film->rating_color }} fs-6">{{ $film->rating }}</span>
                @if($film->release_year)
                    <span class="badge bg-secondary fs-6 ms-2">{{ $film->release_year }}</span>
                @endif
            </div>
            <p class="lead text-muted">{{ $film->age_category }}</p>
        </div>
        <div class="col-auto">
            <div class="btn-group">
                @auth
                    @if(Auth::user()->role === 'employee')
                        <a href="{{ route('films.edit', $film) }}" class="btn btn-gradient-primary">
                            <i class="fas fa-edit me-2"></i>Editar Película
                        </a>
                        <button type="button" class="btn btn-outline-danger" 
                                data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash me-2"></i>Eliminar
                        </button>
                    @else
                        <!-- Customer Rental Button -->
                        <button type="button" class="btn btn-gradient-success btn-lg" 
                                data-bs-toggle="modal" data-bs-target="#rentalModal">
                            <i class="fas fa-shopping-cart me-2"></i>Alquilar esta Película
                        </button>
                        <a href="{{ route('rentals.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-list me-2"></i>Mis Alquileres
                        </a>
                    @endif
                @endauth
                @guest
                    <a href="{{ route('login') }}" class="btn btn-gradient-primary btn-lg">
                        <i class="fas fa-sign-in-alt me-2"></i>Iniciar sesión para alquilar
                    </a>
                @endguest
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Film Details Card -->
            <div class="card shadow-custom border-0 mb-4">
                <div class="card-header bg-gradient-primary text-white border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Detalles de la Película
                    </h5>
                </div>
                <div class="card-body">
                    @if($film->description)
                        <div class="mb-4">
                            <h6 class="fw-bold">Descripción</h6>
                            <p class="text-muted">{{ $film->description }}</p>
                        </div>
                    @endif

                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="fw-bold">Información de Idioma</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td class="fw-bold">Idioma Principal:</td>
                                    <td>{{ $film->language->name ?? 'N/A' }}</td>
                                </tr>
                                @if($film->originalLanguage && $film->originalLanguage->language_id !== $film->language_id)
                                <tr>
                                    <td class="fw-bold">Idioma Original:</td>
                                    <td>{{ $film->originalLanguage->name }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h6 class="fw-bold">Detalles Técnicos</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td class="fw-bold">Duración:</td>
                                    <td>{{ $film->duration_format }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Clasificación:</td>
                                    <td>
                                        <span class="badge bg-{{ $film->rating_color }}">{{ $film->rating }}</span>
                                        <small class="text-muted ms-2">{{ $film->age_category }}</small>
                                    </td>
                                </tr>
                                @if($film->release_year)
                                <tr>
                                    <td class="fw-bold">Año de Lanzamiento:</td>
                                    <td>{{ $film->release_year }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category Card -->
            @if($film->category)
            <div class="card shadow-custom border-0 mb-4">
                <div class="card-header bg-gradient-info text-white border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-tag me-2"></i>Categoría
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('films.by-category', $film->category) }}" 
                           class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-tag me-1"></i>{{ $film->category->name }}
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Special Features Card -->
            @if($film->special_features && count($film->special_features) > 0)
            <div class="card shadow-custom border-0 mb-4">
                <div class="card-header bg-gradient-warning text-white border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-star me-2"></i>Características Especiales
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($film->special_features as $feature)
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    <span>{{ $feature }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Rental Information Card -->
            <div class="card shadow-custom border-0 mb-4">
                <div class="card-header bg-gradient-success text-white border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-dollar-sign me-2"></i>Información de Alquiler
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="display-6 fw-bold text-success">
                            ${{ number_format($film->rental_rate, 2) }}
                        </div>
                        <small class="text-muted">por alquiler</small>
                    </div>
                    
                    <table class="table table-sm">
                        <tr>
                            <td class="fw-bold">Duración del Alquiler:</td>
                            <td>{{ $film->rental_duration }} {{ Str::plural('día', $film->rental_duration) }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Costo de Reposición:</td>
                            <td class="text-danger fw-bold">${{ number_format($film->replacement_cost, 2) }}</td>
                        </tr>
                    </table>

                    <!-- Availability Status -->
                    @php
                        $availableInventory = \App\Models\Inventory::where('film_id', $film->film_id)
                            ->whereNotExists(function ($query) {
                                $query->select(DB::raw(1))
                                      ->from('rentals')
                                      ->whereColumn('rentals.inventory_id', 'inventory.inventory_id')
                                      ->where('rentals.status', 'active');
                            })->count();
                        
                        $totalInventory = \App\Models\Inventory::where('film_id', $film->film_id)->count();
                    @endphp

                    <hr>
                    <div class="text-center">
                        @if($availableInventory > 0)
                            <div class="alert alert-success py-2 mb-2">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>¡Disponible!</strong>
                            </div>
                            <small class="text-muted">
                                {{ $availableInventory }} de {{ $totalInventory }} copias disponibles
                            </small>
                        @else
                            <div class="alert alert-warning py-2 mb-2">
                                <i class="fas fa-clock me-2"></i>
                                <strong>No Disponible</strong>
                            </div>
                            <small class="text-muted">
                                Todas las copias están alquiladas
                            </small>
                        @endif
                    </div>

                    @auth
                        @if(Auth::user()->role === 'customer')
                            @php
                                $customer = \App\Models\Customer::where('email', Auth::user()->email)->first();
                                $hasActiveRental = false;
                                $canRent = true;
                                
                                if ($customer) {
                                    $hasActiveRental = \App\Models\Rental::whereHas('inventory', function($query) use ($film) {
                                        $query->where('film_id', $film->film_id);
                                    })
                                    ->where('customer_id', $customer->customer_id)
                                    ->where('status', 'active')
                                    ->exists();
                                    
                                    $canRent = $customer->canRent();
                                }
                            @endphp

                            <div class="d-grid mt-3">
                                @if($hasActiveRental)
                                    <button class="btn btn-outline-secondary" disabled>
                                        <i class="fas fa-ban me-2"></i>Ya la tienes alquilada
                                    </button>
                                @elseif(!$canRent)
                                    <button class="btn btn-outline-danger" disabled>
                                        <i class="fas fa-exclamation-triangle me-2"></i>Pagos pendientes
                                    </button>
                                @elseif($availableInventory > 0)
                                    <button type="button" class="btn btn-gradient-success" 
                                            data-bs-toggle="modal" data-bs-target="#rentalModal">
                                        <i class="fas fa-shopping-cart me-2"></i>Alquilar Ahora
                                    </button>
                                @else
                                    <button class="btn btn-outline-secondary" disabled>
                                        <i class="fas fa-times me-2"></i>No Disponible
                                    </button>
                                @endif
                            </div>
                        @endif
                    @endauth
                </div>
            </div>

            @auth
                @if(Auth::user()->isEmployee())
                    <!-- Quick Actions Card -->
                    <div class="card shadow-custom border-0 mb-4">
                        <div class="card-header bg-gradient-secondary text-white border-0">
                            <h5 class="mb-0">
                                <i class="fas fa-bolt me-2"></i>Acciones Rápidas
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('films.edit', $film) }}" class="btn btn-gradient-primary">
                                    <i class="fas fa-edit me-2"></i>Editar Película
                                </a>
                                
                                @if($film->language)
                                    <a href="{{ route('films.by-language', $film->language) }}" class="btn btn-outline-info">
                                        <i class="fas fa-language me-2"></i>Más Películas en {{ $film->language->name }}
                                    </a>
                                @endif
                                
                                @if($film->rating)
                                    <a href="{{ route('films.by-rating', $film->rating) }}" class="btn btn-outline-warning">
                                        <i class="fas fa-certificate me-2"></i>Más Películas {{ $film->rating }}
                                    </a>
                                @endif
                                
                                @if($film->release_year)
                                    <a href="{{ route('films.by-decade', floor($film->release_year / 10) * 10) }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-calendar me-2"></i>Películas de los {{ floor($film->release_year / 10) * 10 }}s
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Film Statistics Card -->
                    <div class="card shadow-custom border-0">
                        <div class="card-header bg-gradient-dark text-white border-0">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-bar me-2"></i>Estadísticas de la Película
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3 text-center">
                                <div class="col-12">
                                    <div class="border rounded p-2">
                                        <div class="fw-bold text-primary">ID de Película</div>
                                        <div class="h5 mb-0">#{{ $film->film_id }}</div>
                                    </div>
                                </div>
                                
                                @if($film->length)
                                <div class="col-6">
                                    <div class="border rounded p-2">
                                        <div class="fw-bold text-info">Duración</div>
                                        <div class="h6 mb-0">{{ $film->length }} min</div>
                                    </div>
                                </div>
                                @endif
                                
                                <div class="col-6">
                                    <div class="border rounded p-2">
                                        <div class="fw-bold text-success">Categoría</div>
                                        <div class="h6 mb-0">{{ $film->category ? $film->category->name : 'Ninguna' }}</div>
                                    </div>
                                </div>
                                
                                <div class="col-6">
                                    <div class="border rounded p-2">
                                        <div class="fw-bold text-warning">Características</div>
                                        <div class="h6 mb-0">{{ $film->special_features ? count($film->special_features) : 0 }}</div>
                                    </div>
                                </div>
                                
                                <div class="col-6">
                                    <div class="border rounded p-2">
                                        <div class="fw-bold text-danger">Ratio de Costo</div>
                                        <div class="h6 mb-0">{{ number_format($film->replacement_cost / $film->rental_rate, 1) }}x</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endauth
        </div>
    </div>
</div>

<!-- Rental Confirmation Modal -->
@auth
@if(Auth::user()->role === 'customer')
<div class="modal fade" id="rentalModal" tabindex="-1" aria-labelledby="rentalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-success text-white">
                <h5 class="modal-title" id="rentalModalLabel">
                    <i class="fas fa-shopping-cart me-2"></i>Confirmar Alquiler de Película
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 text-center mb-3">
                        <img src="{{ $film->poster_url ?? asset('images/default-poster.jpg') }}" 
                             alt="{{ $film->title }}" class="img-fluid rounded shadow" 
                             style="max-height: 200px; object-fit: cover;">
                    </div>
                    <div class="col-md-8">
                        <h4 class="text-primary">{{ $film->title }}</h4>
                        @if($film->release_year)
                            <p class="text-muted mb-2">{{ $film->release_year }}</p>
                        @endif
                        
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="card bg-light">
                                    <div class="card-body p-3">
                                        <h6 class="card-title text-success mb-1">
                                            <i class="fas fa-dollar-sign me-1"></i>Costo de Alquiler
                                        </h6>
                                        <div class="h4 mb-0">${{ number_format($film->rental_rate, 2) }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="card bg-light">
                                    <div class="card-body p-3">
                                        <h6 class="card-title text-info mb-1">
                                            <i class="fas fa-calendar me-1"></i>Duración
                                        </h6>
                                        <div class="h6 mb-0">{{ $film->rental_duration }} {{ Str::plural('día', $film->rental_duration) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info mt-3">
                            <h6><i class="fas fa-info-circle me-2"></i>Detalles del Alquiler:</h6>
                            <ul class="mb-0">
                                <li>Fecha de vencimiento: <strong>{{ now()->addDays($film->rental_duration)->format('d/m/Y') }}</strong></li>
                                <li>Costo de reposición si se daña o pierde: <strong class="text-danger">${{ number_format($film->replacement_cost, 2) }}</strong></li>
                                <li>El alquiler aparecerá inmediatamente en "Mis Alquileres"</li>
                            </ul>
                        </div>
                        
                        @if($film->description)
                            <div class="mt-3">
                                <h6>Sinopsis:</h6>
                                <p class="text-muted small">{{ Str::limit($film->description, 200) }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancelar
                </button>
                <form action="{{ route('rentals.rent-film', $film) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-gradient-success">
                        <i class="fas fa-check me-2"></i>Confirmar Alquiler - ${{ number_format($film->rental_rate, 2) }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endauth

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que quieres eliminar la película <strong>"{{ $film->title }}"</strong>?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Esta acción no se puede deshacer. La película será eliminada permanentemente de la colección.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-gradient-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="{{ route('films.destroy', $film) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Eliminar Película
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection