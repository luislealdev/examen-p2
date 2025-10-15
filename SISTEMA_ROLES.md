# 🎭 Sistema de Roles - VideoStore

## 📋 Resumen del Sistema

El sistema **SÍ está contemplando** y **completamente implementando** los tres roles solicitados:

-   **🔴 Admin** - Acceso completo al sistema
-   **🟡 Employee** - Acceso a gestión operativa
-   **🟢 Customer** - Acceso limitado como cliente

---

## 👥 Roles Implementados

### 🔴 **ADMIN**

**Nivel de acceso: MÁXIMO (Nivel 3)**

**Permisos específicos:**

-   ✅ Todo lo que puede hacer un Employee
-   ✅ Gestión de Personal (Staff)
-   ✅ Acceso completo a todas las tiendas
-   ✅ Reportes administrativos avanzados
-   ✅ Configuración del sistema

**Funcionalidades únicas:**

-   Crear/editar/eliminar empleados
-   Ver estadísticas globales
-   Gestión de usuarios y roles
-   Configuración de OMDB API

### 🟡 **EMPLOYEE**

**Nivel de acceso: MEDIO (Nivel 2)**

**Permisos específicos:**

-   ✅ Gestión completa de inventarios
-   ✅ CRUD de películas, categorías, idiomas
-   ✅ Gestión de clientes y alquileres
-   ✅ Ver tiendas (limitado a su tienda si no es admin)
-   ✅ Reportes operativos
-   ❌ NO puede gestionar personal

**Funcionalidades principales:**

-   Dashboard operativo
-   Inventario por tienda/película
-   Procesar alquileres y devoluciones
-   Estadísticas de films

### 🟢 **CUSTOMER**

**Nivel de acceso: BÁSICO (Nivel 1)**

**Permisos específicos:**

-   ✅ Ver catálogo de películas
-   ✅ Ver tiendas disponibles
-   ✅ Ver sus propios alquileres
-   ✅ Perfil personal
-   ❌ NO acceso administrativo
-   ❌ NO gestión de inventarios

**Funcionalidades principales:**

-   Navegación de catálogo
-   Historial personal de alquileres
-   Información de tiendas

---

## 🛡️ Implementación Técnica

### **Middleware de Roles**

```php
// app/Http/Middleware/CheckRole.php
protected $roleHierarchy = [
    'admin' => 3,      // Nivel más alto
    'employee' => 2,   // Nivel medio
    'customer' => 1,   // Nivel base
];
```

### **Protección de Rutas**

```php
// routes/web.php
Route::middleware(['auth', 'role:employee'])->group(function () {
    // Rutas solo para empleados y admins
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    // Rutas solo para admins
});
```

### **Métodos en User Model**

```php
// app/Models/User.php
public function isAdmin(): bool
public function isEmployee(): bool
public function isCustomer(): bool
```

---

## 🎯 Navegación por Rol

### **Admin ve:**

-   📊 Dashboard completo
-   📦 Inventarios
-   🏷️ Categorías & Idiomas
-   🏪 Tiendas
-   👥 Clientes
-   👔 **Personal (exclusivo)**
-   📈 Estadísticas avanzadas

### **Employee ve:**

-   📊 Dashboard operativo
-   📦 Inventarios
-   🏷️ Categorías & Idiomas
-   🏪 Tiendas
-   👥 Clientes
-   📈 Estadísticas básicas

### **Customer ve:**

-   🎬 Catálogo de películas
-   🏪 Tiendas disponibles
-   🎫 Mis alquileres
-   👤 Mi perfil

---

## 🔐 Usuarios de Prueba

```bash
# ADMIN
Email: admin@videostore.com
Password: password
Rol: admin

# EMPLOYEE
Email: employee@videostore.com
Password: password
Rol: employee

# CUSTOMER
Email: cliente@test.com
Password: password
Rol: customer
```

---

## ✅ Verificación del Sistema

1. **Jerarquía implementada**: Admin > Employee > Customer
2. **Middleware funcional**: Protege rutas por nivel de acceso
3. **UI diferenciada**: Navegación específica por rol
4. **Base de datos**: Campo `role` en tabla `users`
5. **Scopes de consulta**: Filtrado automático por permisos
6. **Validaciones**: Acceso denegado con mensaje 403

---

## 🚀 Pruebas Recomendadas

1. **Login como Admin**: Verificar acceso completo
2. **Login como Employee**: Confirmar limitaciones (no gestión de staff)
3. **Login como Customer**: Solo navegación básica
4. **Intentar acceso no autorizado**: Ver mensaje 403
5. **Navegación**: Menús diferentes por rol

**El sistema está completamente funcional y cumple con los requisitos de roles especificados.**
