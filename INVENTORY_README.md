# Sistema de Gestión de Inventario

## Descripción General

El sistema de gestión de inventario permite administrar el stock de películas en múltiples tiendas, proporcionando funcionalidades completas para crear, editar, eliminar y consultar artículos de inventario.

## Estructura de Base de Datos

### Tabla `inventory`

-   `inventory_id` (PK): ID único del artículo de inventario
-   `film_id` (FK): Referencia a la película
-   `store_id` (FK): Referencia a la tienda
-   `last_update`: Timestamp de última actualización

### Relaciones

-   **Film**: Cada artículo pertenece a una película (`belongsTo`)
-   **Store**: Cada artículo pertenece a una tienda (`belongsTo`)
-   **Rentals**: Un artículo puede tener múltiples alquileres (`hasMany`)

## Funcionalidades Implementadas

### 1. CRUD Básico

-   **Crear**: Agregar nuevos artículos de inventario
-   **Leer**: Visualizar inventario con filtros avanzados
-   **Actualizar**: Editar artículos existentes
-   **Eliminar**: Remover artículos del inventario

### 2. Vistas Especializadas

#### Index Principal (`/inventories`)

-   Lista paginada del inventario completo
-   Filtros por película, tienda, clasificación, categoría, idioma
-   Búsqueda por texto en título y descripción
-   Ordenamiento múltiple
-   Estadísticas en tiempo real

#### Por Película (`/inventories-film/{film}`)

-   Vista detallada del inventario de una película específica
-   Información completa de la película con poster
-   Estadísticas por tienda para esa película
-   Distribución de copias por ubicación

#### Por Tienda (`/inventories-store/{store}`)

-   Inventario completo de una tienda específica
-   Información del gerente y dirección
-   Métricas de eficiencia
-   Filtros específicos de la tienda

#### Adiciones Recientes (`/inventories-recent`)

-   Artículos agregados en los últimos X días
-   Gráfico de actividad diaria
-   Filtros por período de tiempo
-   Estadísticas de tendencias

#### Alto Valor (`/inventories-high-value`)

-   Artículos con tarifa de alquiler ≥ $4.00
-   Distribución por rangos de precio
-   Top 5 películas más valiosas
-   Análisis de valor de inventario

#### Estadísticas (`/inventories-statistics`)

-   Dashboard completo con métricas
-   Distribución por tienda y clasificación
-   Resumen detallado por tienda
-   Acciones rápidas

#### Creación en Lote (`/inventories-bulk-create`)

-   Agregar múltiples copias simultáneamente
-   Selección de película y tiendas
-   Vista previa interactiva
-   Validación en tiempo real

### 3. Funcionalidades Avanzadas

#### Scopes del Modelo

-   `search()`: Búsqueda por título y descripción
-   `byFilm()`: Filtrar por película
-   `byStore()`: Filtrar por tienda
-   `byFilmRating()`: Filtrar por clasificación
-   `byFilmCategory()`: Filtrar por categoría
-   `byFilmLanguage()`: Filtrar por idioma
-   `recent()`: Artículos recientes
-   `alphabetical()`: Orden alfabético
-   `newest()`: Más recientes primero
-   `oldest()`: Más antiguos primero
-   `highValue()`: Alto valor
-   `available()`: Disponibles

#### Accessors

-   `last_update_format`: Fecha formateada
-   `last_update_human`: Tiempo relativo
-   `film_title`: Título de película seguro
-   `store_location`: Ubicación de tienda
-   `rental_rate`: Tarifa de alquiler
-   `status`: Estado del artículo
-   `status_color`: Color para UI

#### Métodos Estáticos

-   `getStatistics()`: Estadísticas completas
-   `getAvailableCount()`: Copias disponibles por película
-   `getStoreInventorySummary()`: Resumen por tienda

### 4. Comando de Consola

#### `inventory:manage`

Comando artisan personalizado para gestión desde terminal:

```bash
# Ver estadísticas
php artisan inventory:manage stats

# Agregar artículo individual
php artisan inventory:manage add --film=1 --store=1

# Listar inventario
php artisan inventory:manage list --store=1
php artisan inventory:manage list --film=2

# Remover artículos
php artisan inventory:manage remove --film=1 --store=1

# Agregar en lote
php artisan inventory:manage bulk-add --film=1 --store=1 --quantity=5
php artisan inventory:manage bulk-add --film=1 --all-stores --quantity=3
```

### 5. Seeder de Datos

#### `InventorySeeder`

-   Crea automáticamente 1-3 copias de cada película en cada tienda
-   Muestra estadísticas de creación
-   Tabla resumen por tienda

```bash
php artisan db:seed --class=InventorySeeder
```

## Rutas Disponibles

### Públicas (Solo visualización)

-   `GET /films` - Ver catálogo de películas

### Autenticadas (Empleados/Admins)

```php
// CRUD básico
Route::resource('inventories', InventoryController::class);

// Rutas especializadas
Route::get('inventories-film/{film}', [InventoryController::class, 'byFilm']);
Route::get('inventories-store/{store}', [InventoryController::class, 'byStore']);
Route::get('inventories-recent', [InventoryController::class, 'recent']);
Route::get('inventories-high-value', [InventoryController::class, 'highValue']);
Route::get('inventories-statistics', [InventoryController::class, 'statistics']);
Route::get('inventories-bulk-create', [InventoryController::class, 'bulkCreate']);
Route::post('inventories-bulk-store', [InventoryController::class, 'bulkStore']);
```

## Validaciones

### Crear/Editar Artículo

-   `film_id`: Requerido, debe existir en tabla `film`
-   `store_id`: Requerido, debe existir en tabla `stores`

### Creación en Lote

-   `film_id`: Requerido, película válida
-   `stores`: Array requerido, mínimo 1 tienda válida
-   `quantity`: Entero entre 1-50

## Middleware y Autorización

### Roles Requeridos

-   **Customer**: Solo visualización de catálogo
-   **Employee/Admin**: Acceso completo a gestión de inventario

### Middleware Aplicado

-   `auth`: Usuario autenticado
-   `role:employee`: Rol de empleado o superior

## Características de UI

### Diseño Responsivo

-   Bootstrap 5 con clases personalizadas
-   Gradientes CSS para botones
-   Cards con sombras personalizadas
-   Iconos Font Awesome

### Componentes Interactivos

-   Filtros en tiempo real
-   Paginación con parámetros preservados
-   Modales de confirmación
-   Progress bars animados
-   Tooltips informativos

### Estados Visuales

-   Badges de estado coloreados
-   Indicadores de clasificación
-   Barras de progreso para métricas
-   Placeholders para imágenes faltantes

## Optimizaciones de Rendimiento

### Eager Loading

```php
Inventory::with(['film.language', 'film.category', 'store'])
```

### Índices de Base de Datos

-   `inventory_film_id_index`
-   `inventory_store_id_index`
-   `inventory_film_id_store_id_index`
-   `inventory_last_update_index`

### Paginación

-   20 elementos por página por defecto
-   Parámetros de consulta preservados
-   Lazy loading para grandes datasets

## Archivos Principales

### Controladores

-   `app/Http/Controllers/InventoryController.php`

### Modelos

-   `app/Models/Inventory.php`
-   `app/Models/Film.php` (actualizado)
-   `app/Models/Store.php`
-   `app/Models/Language.php` (actualizado)

### Vistas

-   `resources/views/inventories/index.blade.php`
-   `resources/views/inventories/create.blade.php`
-   `resources/views/inventories/edit.blade.php`
-   `resources/views/inventories/show.blade.php`
-   `resources/views/inventories/by-film.blade.php`
-   `resources/views/inventories/by-store.blade.php`
-   `resources/views/inventories/recent.blade.php`
-   `resources/views/inventories/high-value.blade.php`
-   `resources/views/inventories/statistics.blade.php`
-   `resources/views/inventories/bulk-create.blade.php`

### Comandos

-   `app/Console/Commands/InventoryManagement.php`

### Seeders

-   `database/seeders/InventorySeeder.php`

### Migraciones

-   `database/migrations/2025_10_07_224533_create_inventory_table.php`

## Próximas Mejoras

1. **Integración con Alquileres**

    - Estado real de disponibilidad
    - Reservas y pre-alquileres
    - Historial de movimientos

2. **Reportes Avanzados**

    - Exportación PDF/Excel
    - Análisis de tendencias
    - Alertas de stock bajo

3. **API RESTful**

    - Endpoints JSON
    - Documentación Swagger
    - Rate limiting

4. **Notificaciones**

    - Alertas por email
    - Notificaciones push
    - Slack/Teams integration

5. **Auditoría**
    - Log de cambios
    - Historial de modificaciones
    - Trazabilidad completa
