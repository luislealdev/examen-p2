# Sistema de Pruebas - Movie Rental System

## Descripción General

Este sistema incluye pruebas unitarias y funcionales para validar la lógica de negocio crítica del sistema de alquiler de películas.

## Tipos de Pruebas

### 1. Pruebas Unitarias
**Ubicación:** `tests/Unit/`

**Propósito:** Probar la lógica de negocio individual de los modelos y componentes.

**Archivo Principal:** `FilmTest.php`

**Qué se prueba:**
- Creación de películas con atributos requeridos
- Relaciones entre modelos (Film ↔ Language, Film ↔ Category)
- Gestión de inventario y disponibilidad
- Cálculo de estados de disponibilidad
- Categorización por edad de películas
- Funcionalidad de búsqueda

### 2. Pruebas Funcionales
**Ubicación:** `tests/Feature/`

**Propósito:** Probar flujos completos del sistema y endpoints de API.

**Archivos:**
- `FilmApiTest.php` - Pruebas de la API pública
- `RentalFlowTest.php` - Flujos de registro, renta y devolución

## Cómo Ejecutar las Pruebas

### Ejecutar Todas las Pruebas
```bash
php artisan test
```

### Ejecutar Solo Pruebas Unitarias
```bash
php artisan test tests/Unit/
```

### Ejecutar Solo Pruebas Funcionales
```bash
php artisan test tests/Feature/
```

### Ejecutar un Archivo Específico
```bash
php artisan test tests/Unit/FilmTest.php
```

### Ejecutar con Información Detallada
```bash
php artisan test --verbose
```

### Ejecutar con Cobertura (si está configurado)
```bash
php artisan test --coverage
```

## Estructura de las Pruebas

### Pruebas Unitarias de Film Model

#### test_can_create_a_film_with_required_attributes()
- **Propósito:** Verifica que se puede crear una película con todos los atributos requeridos
- **Valida:** Título, tarifa de alquiler, clasificación

#### test_belongs_to_a_language()
- **Propósito:** Verifica la relación Film ↔ Language
- **Valida:** Que una película pertenece a un idioma específico

#### test_belongs_to_a_category()
- **Propósito:** Verifica la relación Film ↔ Category
- **Valida:** Que una película pertenece a una categoría específica

#### test_can_have_inventory_items()
- **Propósito:** Verifica que una película puede tener inventario asociado
- **Valida:** Relación Film ↔ Inventory

#### test_can_check_if_has_available_inventory()
- **Propósito:** Verifica la lógica de disponibilidad de inventario
- **Valida:** Atributo `has_available_inventory`

#### test_can_get_availability_status_correctly()
- **Propósito:** Verifica los estados de disponibilidad
- **Valida:** Estados: "Sin inventario", "Totalmente disponible", "Parcialmente disponible"

#### test_can_get_availability_class_for_ui()
- **Propósito:** Verifica las clases CSS para la interfaz
- **Valida:** Clases: "warning", "success", "danger", "info"

#### test_can_scope_films_with_available_inventory()
- **Propósito:** Verifica el scope para filtrar películas disponibles
- **Valida:** Query scope `withAvailableInventory()`

#### test_can_get_age_category_correctly()
- **Propósito:** Verifica la categorización por edad basada en año de lanzamiento
- **Valida:** Categorías: "New Release", "Recent", "Classic", "Vintage"

#### test_can_search_films_by_title_and_description()
- **Propósito:** Verifica la funcionalidad de búsqueda
- **Valida:** Query scope `search()`

## Interpretación de Resultados

### Resultado Exitoso
```
✓ can create a film with required attributes (0.01s)
✓ belongs to a language (0.01s)
...
Tests: 10 passed (25 assertions)
```

### Resultado con Fallas
```
⨯ can create a film with required attributes (0.01s)
Failed asserting that 'Action' is identical to 'Comedy'.
```

## Datos de Prueba

Las pruebas utilizan **Laravel Factories** para generar datos de prueba:

- `LanguageFactory` - Genera idiomas aleatorios
- `CategoryFactory` - Genera categorías de películas
- `FilmFactory` - Genera películas completas
- `InventoryFactory` - Genera elementos de inventario
- `CustomerFactory` - Genera clientes
- `StaffFactory` - Genera empleados
- `RentalFactory` - Genera alquileres

## Base de Datos de Pruebas

Las pruebas utilizan:
- **RefreshDatabase trait** - Limpia la base de datos entre pruebas
- **SQLite en memoria** - Base de datos rápida para testing
- **Transacciones** - Rollback automático después de cada test

## Lógica de Negocio Validada

### 1. Gestión de Inventario
- Estados de condición: `available`, `damaged`, `lost`
- Disponibilidad basada en alquileres activos
- Conteo de inventario disponible vs total

### 2. Estados de Disponibilidad
- **"Sin inventario"** - No hay copias de la película
- **"Totalmente disponible"** - Todas las copias están disponibles
- **"Parcialmente disponible"** - Algunas copias están alquiladas
- **"No disponible"** - Todas las copias están alquiladas

### 3. Categorización por Edad
Basada en años desde el lanzamiento:
- **New Release** - Menos de 5 años
- **Recent** - 5-14 años
- **Classic** - 15-29 años  
- **Vintage** - 30+ años

### 4. Búsqueda y Filtrado
- Búsqueda por título y descripción
- Filtrado por disponibilidad
- Query scopes reutilizables

## Mantenimiento de Pruebas

### Agregar Nuevas Pruebas
1. Crear nuevos métodos que inicien con `test_`
2. Usar nomenclatura descriptiva
3. Seguir patrón: Arrange → Act → Assert
4. Usar factories para datos de prueba

### Actualizar Pruebas Existentes
1. Mantener coherencia con la lógica de negocio
2. Actualizar assertions cuando cambien los valores esperados
3. Refactorizar cuando sea necesario

### Problemas Comunes
1. **Foreign key constraints** - Asegurar que existan registros dependientes
2. **Unique constraints** - Usar `fake()->unique()` en factories
3. **404 en API tests** - Verificar que las rutas estén registradas correctamente

## Comandos Útiles de Depuración

```bash
# Ver detalles de fallas
php artisan test --stop-on-failure

# Ejecutar tests específicos por nombre
php artisan test --filter="test_can_create_a_film"

# Ver información de la base de datos durante tests
php artisan test --verbose
```

Esta documentación debe actualizarse cuando se agreguen nuevas pruebas o cambien los requisitos del sistema.