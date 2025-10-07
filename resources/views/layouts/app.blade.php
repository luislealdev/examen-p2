<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Store Management')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('films.index') }}">
                <i class="fas fa-film me-2"></i>Sakila Movies
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav me-auto">
                    <a class="nav-link" href="{{ route('films.index') }}">
                        <i class="fas fa-film me-1"></i>Films
                    </a>
                    <a class="nav-link" href="{{ route('categories.index') }}">
                        <i class="fas fa-tags me-1"></i>Categories
                    </a>
                    <a class="nav-link" href="{{ route('languages.index') }}">
                        <i class="fas fa-language me-1"></i>Languages
                    </a>
                    <a class="nav-link" href="{{ route('stores.index') }}">
                        <i class="fas fa-store me-1"></i>Stores
                    </a>
                    <a class="nav-link" href="{{ route('customers.index') }}">
                        <i class="fas fa-users me-1"></i>Customers
                    </a>
                    <a class="nav-link" href="{{ route('staff.index') }}">
                        <i class="fas fa-user-tie me-1"></i>Staff
                    </a>
                </div>
                <div class="navbar-nav">
                    <a class="nav-link" href="{{ route('films.statistics') }}">
                        <i class="fas fa-chart-bar me-1"></i>Statistics
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>