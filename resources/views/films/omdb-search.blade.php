@extends('layouts.app')

@section('title', 'Buscar Películas en OMDB')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">
                    <i class="fas fa-search me-2"></i>
                    Buscar Películas en OMDB
                </h2>
                <div class="d-flex gap-2">
                    <a href="{{ route('films.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Volver a Películas
                    </a>
                    <a href="{{ route('films.create') }}" class="btn btn-gradient-primary">
                        <i class="fas fa-plus me-1"></i>
                        Crear Película Manual
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Configuración Alert -->
    <div id="config-alert" class="alert alert-warning d-none">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Configuración Requerida:</strong> Necesita configurar OMDB_API_KEY en el archivo .env
    </div>

    <!-- Búsqueda -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-film me-2"></i>
                Buscar Películas Externas
            </h5>
        </div>
        <div class="card-body">
            <form id="search-form" onsubmit="return false;">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label for="search-query" class="form-label">Título de la Película</label>
                        <input type="text" class="form-control" id="search-query" 
                               placeholder="Ej: The Matrix, Inception, Avatar..."
                               required minlength="2">
                        <div class="form-text">
                            Ingrese al menos 2 caracteres para buscar
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-gradient-primary" id="search-btn">
                                <i class="fas fa-search me-1"></i>
                                Buscar
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Loading -->
    <div id="loading" class="text-center d-none">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Buscando...</span>
        </div>
        <p class="mt-2">Buscando películas en OMDB...</p>
    </div>

    <!-- Resultados -->
    <div id="results-container" class="d-none">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    Resultados de Búsqueda
                </h5>
                <div id="results-info" class="text-muted"></div>
            </div>
            <div class="card-body">
                <div id="results-grid" class="row"></div>
                
                <!-- Paginación -->
                <div id="pagination" class="d-flex justify-content-center mt-4 d-none">
                    <nav>
                        <ul class="pagination">
                            <li class="page-item" id="prev-page">
                                <a class="page-link" href="#">Anterior</a>
                            </li>
                            <li class="page-item" id="next-page">
                                <a class="page-link" href="#">Siguiente</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Message -->
    <div id="error-message" class="alert alert-danger d-none">
        <i class="fas fa-exclamation-circle me-2"></i>
        <span id="error-text"></span>
    </div>
</div>

<!-- Modal de Previsualización -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-eye me-2"></i>
                    Previsualización de Importación
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="preview-content">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-gradient-primary" id="confirm-import">
                    <i class="fas fa-download me-1"></i>
                    Confirmar Importación
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Importación Exitosa -->
<div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle text-success me-2"></i>
                    Importación Exitosa
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="success-message"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <a href="#" class="btn btn-gradient-primary" id="view-film-btn">
                    <i class="fas fa-eye me-1"></i>
                    Ver Película
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Verificar si jQuery está disponible
if (typeof $ === 'undefined') {
    console.error('jQuery no está disponible');
    alert('Error: jQuery no está cargado. La búsqueda no funcionará.');
} else {
    console.log('jQuery disponible, iniciando aplicación OMDB');
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded');
    console.log('Bootstrap disponible:', typeof bootstrap !== 'undefined');
    console.log('jQuery disponible:', typeof $ !== 'undefined');
    
    // Variables globales
    let currentPage = 1;
    let currentQuery = '';
    let currentImdbId = '';

    // Verificar configuración al cargar la página
    checkConfiguration();

    // Función para verificar configuración
    function checkConfiguration() {
        fetch('{{ route("omdb.check-config") }}')
            .then(response => response.json())
            .then(data => {
                if (!data.configured) {
                    document.getElementById('config-alert').classList.remove('d-none');
                    document.querySelectorAll('#search-form input, #search-form button').forEach(el => {
                        el.disabled = true;
                    });
                }
            })
            .catch(error => {
                console.error('Error verificando configuración:', error);
                document.getElementById('config-alert').classList.remove('d-none');
                document.querySelectorAll('#search-form input, #search-form button').forEach(el => {
                    el.disabled = true;
                });
            });
    }

    // Manejar búsqueda
    document.getElementById('search-form').addEventListener('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('Formulario enviado - preventDefault ejecutado');
        
        currentQuery = document.getElementById('search-query').value.trim();
        currentPage = 1;
        console.log('Query extraído:', currentQuery, 'Página:', currentPage);
        
        if (currentQuery.length < 2) {
            console.log('Query muy corto, mostrando error');
            showError('Ingrese al menos 2 caracteres para buscar');
            return false;
        }

        console.log('Llamando a searchMovies()');
        searchMovies();
        return false;
    });

    // Función de búsqueda
    function searchMovies() {
        console.log('=== INICIANDO BÚSQUEDA ===');
        console.log('Query actual:', currentQuery);
        console.log('Página actual:', currentPage);
        
        showLoading();
        hideError();
        hideResults();

        console.log('Estados UI actualizados, preparando petición...');

        // Crear FormData para enviar como form data tradicional
        const formData = new FormData();
        formData.append('query', currentQuery);
        formData.append('page', currentPage);
        formData.append('_token', '{{ csrf_token() }}');

        console.log('FormData creado con:');
        console.log('- query:', formData.get('query'));
        console.log('- page:', formData.get('page'));
        console.log('- token:', formData.get('_token') ? 'presente' : 'ausente');

        const url = '{{ route("omdb.search-movies") }}';
        console.log('URL destino:', url);

        fetch(url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => {
            console.log('=== RESPUESTA RECIBIDA ===');
            console.log('Response status:', response.status);
            console.log('Response ok:', response.ok);
            console.log('Response headers:', response.headers);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('=== DATOS PARSEADOS ===');
            console.log('Respuesta completa:', data);
            console.log('Success:', data.success);
            console.log('Movies count:', data.movies ? data.movies.length : 'N/A');
            
            hideLoading();
            
            if (data.success) {
                console.log('Llamando a showResults...');
                showResults(data);
            } else {
                console.log('Error en respuesta:', data.error);
                showError(data.error || 'Error en la búsqueda');
            }
        })
        .catch(error => {
            console.log('=== ERROR EN PETICIÓN ===');
            console.log('Error object:', error);
            console.log('Error message:', error.message);
            console.log('Error stack:', error.stack);
            
            hideLoading();
            showError('Error de conexión: ' + error.message);
        });
        
        console.log('Petición fetch enviada');
    }

    // Mostrar resultados
    function showResults(data) {
        console.log('Mostrando resultados:', data);
        document.getElementById('results-container').classList.remove('d-none');
        document.getElementById('results-info').textContent = `${data.total_results} resultados encontrados`;
        
        const grid = document.getElementById('results-grid');
        grid.innerHTML = '';

        if (data.movies.length === 0) {
            grid.innerHTML = '<div class="col-12"><p class="text-center text-muted">No se encontraron películas</p></div>';
            return;
        }

        data.movies.forEach(function(movie) {
            const card = createMovieCard(movie);
            grid.appendChild(card);
        });

        // Mostrar paginación si hay más de 10 resultados
        if (data.total_results > 10) {
            setupPagination(data);
        }
    }

    // Crear tarjeta de película
    function createMovieCard(movie) {
        const poster = movie.Poster !== 'N/A' ? movie.Poster : '/placeholder-movie.svg';
        
        const col = document.createElement('div');
        col.className = 'col-md-6 col-lg-4 mb-4';
        
        col.innerHTML = `
            <div class="card h-100 shadow-sm movie-card">
                <img src="${poster}" class="card-img-top" style="height: 300px; object-fit: cover;" 
                     onerror="this.src='/placeholder-movie.svg'">
                <div class="card-body d-flex flex-column">
                    <h6 class="card-title">${movie.Title}</h6>
                    <p class="card-text text-muted">
                        <small>
                            <i class="fas fa-calendar me-1"></i>${movie.Year}<br>
                            <i class="fas fa-film me-1"></i>${movie.Type}<br>
                            <i class="fas fa-hashtag me-1"></i>${movie.imdbID}
                        </small>
                    </p>
                    <div class="mt-auto">
                        <button class="btn btn-gradient-primary btn-sm w-100 preview-btn" 
                                data-imdb-id="${movie.imdbID}">
                            <i class="fas fa-eye me-1"></i>
                            Previsualizar Importación
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        return col;
    }

    // Funciones auxiliares
    function showLoading() {
        console.log('Mostrando loading...');
        const loadingElement = document.getElementById('loading');
        if (loadingElement) {
            loadingElement.classList.remove('d-none');
            console.log('Loading mostrado');
        } else {
            console.error('Elemento loading no encontrado');
        }
    }

    function hideLoading() {
        console.log('Ocultando loading...');
        const loadingElement = document.getElementById('loading');
        if (loadingElement) {
            loadingElement.classList.add('d-none');
            console.log('Loading oculto');
        }
    }

    function hideResults() {
        console.log('Ocultando resultados...');
        const resultsElement = document.getElementById('results-container');
        if (resultsElement) {
            resultsElement.classList.add('d-none');
            console.log('Resultados ocultos');
        }
    }

    function showError(message) {
        console.log('Mostrando error:', message);
        const errorTextElement = document.getElementById('error-text');
        const errorMessageElement = document.getElementById('error-message');
        
        if (errorTextElement && errorMessageElement) {
            errorTextElement.textContent = message;
            errorMessageElement.classList.remove('d-none');
            console.log('Error mostrado');
        } else {
            console.error('Elementos de error no encontrados');
            alert('Error: ' + message); // Fallback
        }
    }

    function hideError() {
        console.log('Ocultando error...');
        const errorMessageElement = document.getElementById('error-message');
        if (errorMessageElement) {
            errorMessageElement.classList.add('d-none');
        }
    }

    function setupPagination(data) {
        const totalPages = Math.ceil(data.total_results / 10);
        
        const prevBtn = document.getElementById('prev-page');
        const nextBtn = document.getElementById('next-page');
        
        if (currentPage === 1) {
            prevBtn.classList.add('disabled');
        } else {
            prevBtn.classList.remove('disabled');
        }
        
        if (currentPage >= totalPages) {
            nextBtn.classList.add('disabled');
        } else {
            nextBtn.classList.remove('disabled');
        }
        
        document.getElementById('pagination').classList.remove('d-none');
    }

    // Event delegation para botones dinámicos
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('preview-btn') || e.target.closest('.preview-btn')) {
            const btn = e.target.classList.contains('preview-btn') ? e.target : e.target.closest('.preview-btn');
            const imdbId = btn.getAttribute('data-imdb-id');
            currentImdbId = imdbId;
            
            const formData = new FormData();
            formData.append('imdb_id', imdbId);
            formData.append('_token', '{{ csrf_token() }}');
            
            fetch('{{ route("omdb.preview-import") }}', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showPreview(data.preview);
                } else {
                    showError(data.error || 'Error obteniendo previsualización');
                }
            })
            .catch(error => {
                showError('Error de conexión: ' + error.message);
            });
        }
    });

    // Funciones que requieren jQuery para modales Bootstrap
    function showPreview(preview) {
        const poster = preview.poster !== 'N/A' ? preview.poster : '/placeholder-movie.svg';
        
        document.getElementById('preview-content').innerHTML = `
            <div class="row">
                <div class="col-md-4">
                    <img src="${poster}" class="img-fluid rounded" 
                         onerror="this.src='/placeholder-movie.svg'">
                </div>
                <div class="col-md-8">
                    <h4>${preview.title} (${preview.year})</h4>
                    <p><strong>Duración:</strong> ${preview.runtime} minutos</p>
                    <p><strong>Géneros:</strong> ${preview.genres.join(', ')}</p>
                    <p><strong>Director:</strong> ${preview.director}</p>
                    <p><strong>Actores principales:</strong> ${preview.actors.join(', ')}</p>
                    <p><strong>Idiomas:</strong> ${preview.languages.join(', ')}</p>
                    ${preview.imdb_rating ? `<p><strong>Rating IMDb:</strong> ${preview.imdb_rating}/10</p>` : ''}
                    <p><strong>Sinopsis:</strong></p>
                    <p class="text-muted">${preview.plot}</p>
                </div>
            </div>
        `;
        
        // Usar Bootstrap modal si está disponible
        if (typeof bootstrap !== 'undefined') {
            const modal = new bootstrap.Modal(document.getElementById('previewModal'));
            modal.show();
        } else {
            console.log('Bootstrap no disponible, mostrando modal manualmente');
            const modal = document.getElementById('previewModal');
            modal.style.display = 'block';
            modal.classList.add('show');
            modal.setAttribute('aria-modal', 'true');
            modal.setAttribute('role', 'dialog');
            document.body.classList.add('modal-open');
        }
    }

    // Manejar confirmación de importación
    document.getElementById('confirm-import').addEventListener('click', function() {
        const btn = this;
        console.log('Botón confirmar importación clickeado');
        console.log('IMDb ID a importar:', currentImdbId);
        
        // Deshabilitar botón y mostrar spinner
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Importando...';
        
        const formData = new FormData();
        formData.append('imdb_id', currentImdbId);
        formData.append('_token', '{{ csrf_token() }}');
        
        fetch('{{ route("omdb.import-movie") }}', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => {
            console.log('Response de importación:', response.status);
            // No lanzar error para 400, porque puede ser una película duplicada
            return response.json();
        })
        .then(data => {
            console.log('Datos de importación:', data);
            
            // Cerrar modal de previsualización
            if (typeof bootstrap !== 'undefined') {
                const previewModal = bootstrap.Modal.getInstance(document.getElementById('previewModal'));
                if (previewModal) {
                    previewModal.hide();
                }
            }
            
            if (data.success) {
                console.log('Importación exitosa:', data);
                // Mostrar modal de éxito
                document.getElementById('success-message').textContent = data.message;
                document.getElementById('view-film-btn').href = data.redirect_url;
                
                if (typeof bootstrap !== 'undefined') {
                    const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();
                } else {
                    console.log('Bootstrap no disponible, mostrando modal de éxito manualmente');
                    alert('¡Importación exitosa! ' + data.message);
                    // Redirigir a la película
                    window.location.href = data.redirect_url;
                }
            } else {
                console.log('Error en importación:', data.error);
                // Si la película ya existe, mostrar mensaje especial
                if (data.error && data.error.includes('ya existe') && data.film) {
                    const filmUrl = '{{ route("films.show", ":id") }}'.replace(':id', data.film.film_id);
                    const message = data.error + '. ¿Desea ver la película existente?';
                    if (confirm(message)) {
                        window.location.href = filmUrl;
                    }
                } else {
                    showError(data.error || 'Error durante la importación');
                }
            }
        })
        .catch(error => {
            console.log('Error en importación:', error);
            
            // Cerrar modal de previsualización
            if (typeof bootstrap !== 'undefined') {
                const previewModal = bootstrap.Modal.getInstance(document.getElementById('previewModal'));
                if (previewModal) {
                    previewModal.hide();
                }
            }
            
            showError('Error de conexión: ' + error.message);
        })
        .finally(function() {
            // Restaurar botón
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-download me-1"></i>Confirmar Importación';
        });
    });
});
</script>
@endpush