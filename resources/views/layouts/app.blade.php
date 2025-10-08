<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Gestión de Películas Sakila')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --dark-color: #34495e;
        }
        
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        
        .btn-gradient-primary {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            border: none;
            color: white;
        }
        
        .btn-gradient-primary:hover {
            background: linear-gradient(45deg, var(--secondary-color), var(--primary-color));
            color: white;
        }
        
        .btn-gradient-info {
            background: linear-gradient(45deg, var(--secondary-color), #17a2b8);
            border: none;
            color: white;
        }
        
        .btn-gradient-info:hover {
            background: linear-gradient(45deg, #17a2b8, var(--secondary-color));
            color: white;
        }
        
        .card-header.bg-gradient-primary {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color)) !important;
        }
        
        .card-header.bg-gradient-info {
            background: linear-gradient(45deg, var(--secondary-color), #17a2b8) !important;
        }
        
        .card-header.bg-gradient-success {
            background: linear-gradient(45deg, var(--success-color), #20c997) !important;
        }
        
        .card-header.bg-gradient-warning {
            background: linear-gradient(45deg, var(--warning-color), #fd7e14) !important;
        }
        
        .navbar-dark {
            background: linear-gradient(45deg, var(--dark-color), var(--primary-color)) !important;
        }
        
        .shadow-custom {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(52, 152, 219, 0.1);
        }
        
        .badge-custom {
            padding: 0.5em 0.75em;
            border-radius: 0.5rem;
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ route('films.index') }}">
                <i class="fas fa-film me-2"></i>Sakila Películas
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav me-auto">
                    <a class="nav-link" href="{{ route('films.index') }}">
                        <i class="fas fa-film me-1"></i>Películas
                    </a>
                    <a class="nav-link" href="{{ route('inventories.index') }}">
                        <i class="fas fa-boxes me-1"></i>Inventario
                    </a>
                    <a class="nav-link" href="{{ route('categories.index') }}">
                        <i class="fas fa-tags me-1"></i>Categorías
                    </a>
                    <a class="nav-link" href="{{ route('languages.index') }}">
                        <i class="fas fa-language me-1"></i>Idiomas
                    </a>
                    <a class="nav-link" href="{{ route('stores.index') }}">
                        <i class="fas fa-store me-1"></i>Tiendas
                    </a>
                    <a class="nav-link" href="{{ route('customers.index') }}">
                        <i class="fas fa-users me-1"></i>Clientes
                    </a>
                    <a class="nav-link" href="{{ route('staff.index') }}">
                        <i class="fas fa-user-tie me-1"></i>Personal
                    </a>
                </div>
                <div class="navbar-nav">
                    <a class="nav-link" href="{{ route('films.statistics') }}">
                        <i class="fas fa-chart-bar me-1"></i>Estadísticas
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light mt-5 py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-film me-2"></i>Sakila Películas</h5>
                    <p class="mb-0">Sistema de gestión de películas, inventario y clientes</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <small>&copy; {{ date('Y') }} Sakila Movies. Todos los derechos reservados.</small>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>