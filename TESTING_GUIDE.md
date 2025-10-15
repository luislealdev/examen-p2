# Guía de Pruebas - Sistema de Inventario

## Preparación del Entorno

1. **Verificar Base de Datos**

```bash
# Crear archivo SQLite si no existe
touch database/database.sqlite

# Ejecutar migraciones
php artisan migrate:fresh

# Poblar datos básicos (opcional si no existen)
# Los siguientes comandos crean datos mínimos necesarios
```

2. **Crear Datos de Prueba**

```bash
# Ejecutar seeder de inventario (requiere films y stores)
php artisan db:seed --class=InventorySeeder
```

## URLs de Prueba

Con el servidor corriendo en `http://127.0.0.1:8000`:

### Páginas Principales

-   **Lista de Inventario**: `http://127.0.0.1:8000/inventories`
-   **Crear Artículo**: `http://127.0.0.1:8000/inventories/create`
-   **Estadísticas**: `http://127.0.0.1:8000/inventories-statistics`
-   **Creación en Lote**: `http://127.0.0.1:8000/inventories-bulk-create`

### Vistas Especializadas

-   **Recientes**: `http://127.0.0.1:8000/inventories-recent`
-   **Alto Valor**: `http://127.0.0.1:8000/inventories-high-value`
-   **Por Película**: `http://127.0.0.1:8000/inventories-film/1`
-   **Por Tienda**: `http://127.0.0.1:8000/inventories-store/1`

## Funcionalidades a Probar

### 1. Navegación y Visualización

-   [ ] Lista principal de inventario se carga correctamente
-   [ ] Paginación funciona (si hay más de 20 elementos)
-   [ ] Breadcrumbs navegan correctamente
-   [ ] Estadísticas se muestran en cards

### 2. Filtros y Búsqueda

-   [ ] Filtro por película funciona
-   [ ] Filtro por tienda funciona
-   [ ] Búsqueda por texto encuentra resultados
-   [ ] Filtros se combinan correctamente
-   [ ] Ordenamiento cambia la lista

### 3. Operaciones CRUD

-   [ ] Crear nuevo artículo (requiere autenticación de empleado)
-   [ ] Ver detalles de un artículo
-   [ ] Editar artículo existente
-   [ ] Eliminar artículo con confirmación

### 4. Creación en Lote

-   [ ] Seleccionar película actualiza vista previa
-   [ ] Seleccionar tiendas actualiza contador
-   [ ] Cambiar cantidad actualiza totales
-   [ ] Botón se habilita solo con datos válidos
-   [ ] Creación masiva funciona correctamente

### 5. Vistas Especializadas

-   [ ] Vista por película muestra todas las copias
-   [ ] Vista por tienda filtra correctamente
-   [ ] Vista de recientes respeta filtro de días
-   [ ] Vista de alto valor filtra por precio ≥ $4.00
-   [ ] Estadísticas muestran gráficos y métricas

## Comandos de Terminal

### Gestión Básica

```bash
# Ver estadísticas
php artisan inventory:manage stats

# Listar inventario
php artisan inventory:manage list

# Filtrar por tienda
php artisan inventory:manage list --store=1

# Filtrar por película
php artisan inventory:manage list --film=1
```

### Operaciones de Datos

```bash
# Agregar artículo interactivamente
php artisan inventory:manage add

# Agregar con parámetros
php artisan inventory:manage add --film=1 --store=2

# Agregar en lote a una tienda
php artisan inventory:manage bulk-add --film=1 --store=1 --quantity=3

# Agregar en lote a todas las tiendas
php artisan inventory:manage bulk-add --film=2 --all-stores --quantity=2

# Remover artículos
php artisan inventory:manage remove --film=1 --store=1
```

## Casos de Prueba Específicos

### Validación de Datos

1. **Crear artículo sin película**: Debe mostrar error de validación
2. **Crear artículo sin tienda**: Debe mostrar error de validación
3. **Película inexistente**: Debe mostrar error
4. **Tienda inexistente**: Debe mostrar error

### Funcionalidad de Filtros

1. **Filtro vacío**: Debe mostrar todos los artículos
2. **Múltiples filtros**: Deben combinarse con AND
3. **Búsqueda parcial**: Debe encontrar coincidencias parciales
4. **Sin resultados**: Debe mostrar mensaje apropiado

### Creación en Lote

1. **Sin película**: Botón deshabilitado
2. **Sin tiendas**: Botón deshabilitado
3. **Cantidad inválida**: Debe validar entre 1-50
4. **Éxito**: Debe redirigir con mensaje de confirmación

### Vistas Especializadas

1. **Película sin inventario**: Debe mostrar mensaje de "sin copias"
2. **Tienda sin inventario**: Debe mostrar mensaje de "sin artículos"
3. **Sin recientes**: Debe mostrar mensaje apropiado
4. **Sin alto valor**: Debe mostrar mensaje apropiado

## Autenticación y Autorización

### Roles de Usuario

-   **Guest**: Solo puede ver catálogo público
-   **Customer**: Acceso limitado a sus alquileres
-   **Employee/Admin**: Acceso completo a gestión de inventario

### Pruebas de Acceso

1. **Sin autenticar**: Redirige a login para rutas protegidas
2. **Customer autenticado**: No accede a gestión de inventario
3. **Employee autenticado**: Acceso completo
4. **Admin autenticado**: Acceso completo

## Troubleshooting

### Problemas Comunes

1. **Error "No films or stores found"**

    - Ejecutar: `php artisan db:seed --class=BaseDataSeeder`
    - O crear datos manualmente como se mostró anteriormente

2. **Errores de base de datos**

    - Verificar que `database/database.sqlite` existe
    - Ejecutar: `php artisan migrate:fresh`

3. **Páginas en blanco**

    - Verificar logs: `storage/logs/laravel.log`
    - Verificar permisos de directorios

4. **Filtros no funcionan**
    - Verificar datos de prueba suficientes
    - Verificar parámetros en URL

### Logs Importantes

```bash
# Ver logs de aplicación
tail -f storage/logs/laravel.log

# Limpiar caché si hay problemas
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

## Datos de Ejemplo

### Usuarios de Prueba

-   **Admin**: admin@sakila.com / password
-   **Empleado**: juan.perez@sakila.com / password
-   **Cliente**: test@example.com / password

### IDs de Referencia

-   **Films**: 1, 2, 3 (Matrix, Godfather, Inception)
-   **Stores**: 1, 2
-   **Languages**: 1 (English), 2 (Spanish)
-   **Categories**: 1 (Action), 2 (Drama)

## Métricas de Éxito

### Funcionalidad Básica

-   [ ] Todas las rutas cargan sin errores
-   [ ] CRUD completo funciona
-   [ ] Filtros y búsqueda operan correctamente
-   [ ] Validaciones previenen datos incorrectos

### Experiencia de Usuario

-   [ ] Interfaz intuitiva y responsive
-   [ ] Mensajes de éxito/error claros
-   [ ] Navegación fluida entre secciones
-   [ ] Carga rápida de páginas

### Robustez

-   [ ] Manejo apropiado de casos sin datos
-   [ ] Validación completa de entrada
-   [ ] Rollback automático en errores
-   [ ] Logs detallados para debugging
