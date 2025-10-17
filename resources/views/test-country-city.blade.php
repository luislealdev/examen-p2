<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Test País/Ciudad</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Test de Selección País/Ciudad</h2>
    
    <div class="row">
        <div class="col-md-6">
            <label for="country_id">País:</label>
            <select class="form-select" id="country_id" name="country_id">
                <option value="">Seleccionar país...</option>
                @foreach(\App\Models\Country::orderBy('country')->get() as $country)
                    <option value="{{ $country->country_id }}">{{ $country->country }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="col-md-6">
            <label for="city_id">Ciudad:</label>
            <select class="form-select" id="city_id" name="city_id" disabled>
                <option value="">Primero selecciona un país...</option>
            </select>
        </div>
    </div>
    
    <div class="mt-3">
        <h4>Debug Info:</h4>
        <div id="debug"></div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const countrySelect = document.getElementById('country_id');
        const citySelect = document.getElementById('city_id');
        const debugDiv = document.getElementById('debug');
        
        function log(message) {
            console.log(message);
            debugDiv.innerHTML += '<div>' + message + '</div>';
        }
        
        log('🚀 Test iniciado');
        log('País select: ' + (countrySelect ? 'OK' : 'ERROR'));
        log('Ciudad select: ' + (citySelect ? 'OK' : 'ERROR'));
        
        countrySelect.addEventListener('change', function() {
            const countryId = this.value;
            log('🌍 País seleccionado: ' + countryId);
            
            if (!countryId) {
                citySelect.innerHTML = '<option value="">Primero selecciona un país...</option>';
                citySelect.disabled = true;
                log('🔒 Ciudad select deshabilitado');
                return;
            }
            
            citySelect.innerHTML = '<option value="">Cargando ciudades...</option>';
            citySelect.disabled = true;
            log('⏳ Cargando ciudades...');
            
            fetch(`/cities/by-country?country_id=${countryId}`)
                .then(response => {
                    log('📡 Respuesta: ' + response.status);
                    return response.json();
                })
                .then(cities => {
                    log('🏙️ Ciudades recibidas: ' + cities.length);
                    
                    citySelect.innerHTML = '<option value="">Seleccionar ciudad...</option>';
                    
                    cities.forEach(city => {
                        const option = document.createElement('option');
                        option.value = city.city_id;
                        option.textContent = city.city;
                        citySelect.appendChild(option);
                    });
                    
                    citySelect.disabled = false;
                    log('🔓 Ciudad select HABILITADO - disabled: ' + citySelect.disabled);
                })
                .catch(error => {
                    log('❌ Error: ' + error.message);
                    citySelect.innerHTML = '<option value="">Error al cargar</option>';
                    citySelect.disabled = false;
                });
        });
    });
    </script>
</body>
</html>