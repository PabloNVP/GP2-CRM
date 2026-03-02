# Sprint 1 - Setup del Proyecto y Autenticación

**Módulo:** Infraestructura y Autenticación  
**Duración estimada:** 2 semanas  
**Objetivo:** Crear el proyecto Laravel 12, configurar el entorno de desarrollo, implementar el sistema de registro e inicio de sesión de usuarios con roles.

---

## Alcance del Sprint

### Setup del Proyecto
- [x] Crear proyecto Laravel 12 con Blade + Livewire.
- [x] Configurar SQLite como base de datos.
- [x] Configurar estructura de carpetas y convenciones del proyecto.
- [x] Configurar `.env` de ejemplo y `.env.example`.
- [x] Migración de tabla `users` con campo `rol` y `estado`.
- [x] Seeder con usuario administrador por defecto.

### Vistas — Blade + Livewire (Auth)
- Pantalla de **Login** con campos email y contraseña.
- Pantalla de **Registro** con campos: nombre, email, contraseña, confirmación de contraseña.
- Validaciones en tiempo real en ambos formularios (Livewire).
- Layout principal con navbar, sidebar y área de contenido.
- Navbar con nombre del usuario logueado y botón de logout.
- Redirección al dashboard después del login exitoso.
- Pantalla de **Dashboard** básica (placeholder para futuros módulos).

### Backend — Laravel (Auth)
- **Modelo Eloquent** `User` extendido con campos: rol (enum: operador, soporte, comercial, administrativo, admin, cliente) y estado (enum: activo, inactivo).
- **Migración** que agrega los campos `rol` y `estado` a la tabla `users` de Laravel.
- **Seeder** `AdminSeeder` que crea un usuario admin por defecto.
- **Middleware** `CheckRole` para proteger rutas según rol del usuario.
- **Middleware** `CheckEstado` para impedir acceso a usuarios inactivos.
- **Rutas web**:
  - `GET /login` — Formulario de login.
  - `POST /login` — Procesar login.
  - `GET /register` — Formulario de registro.
  - `POST /register` — Procesar registro.
  - `POST /logout` — Cerrar sesión.
  - `GET /dashboard` — Dashboard (protegida por auth).
- **Controller** `Auth\LoginController` y `Auth\RegisterController`.
- **Form Request** `LoginRequest` y `RegisterRequest` con validaciones.

### Pruebas
- Tests unitarios del modelo `User` (roles, estados).
- Tests Feature de registro (datos válidos, email duplicado, contraseña débil).
- Tests Feature de login (credenciales válidas, inválidas, usuario inactivo).
- Tests Feature de middleware de roles (acceso permitido/denegado).
- Test de logout y redirección.

---

## Story Cards

### SC-01: Crear proyecto Laravel
**Como** desarrollador, **quiero** inicializar el proyecto Laravel 12 con Blade, Livewire y SQLite **para** tener la base del CRM lista.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimación | 3 pts |
| Criterios de aceptación | El proyecto se crea la carpeta del proyecto Laravel. con Livewire instalado y funcional. SQLite configurado en `.env`. La app se levanta con `php artisan serve` sin errores. El `.env.example` está actualizado. |

---

### SC-02: Migración de usuarios con roles
**Como** desarrollador, **quiero** extender la tabla `users` con los campos `rol` y `estado` **para** soportar el control de acceso del CRM.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimación | 2 pts |
| Criterios de aceptación | La migración agrega columnas `rol` (string, default: 'cliente') y `estado` (string, default: 'activo') a la tabla `users`. Se ejecuta y revierte sin errores. El modelo `User` tiene los casts y fillable correspondientes. |

---

### SC-03: Seeder de administrador
**Como** desarrollador, **quiero** tener un usuario administrador pre-cargado **para** poder acceder al sistema desde el inicio.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimación | 1 pt |
| Criterios de aceptación | Al ejecutar `php artisan db:seed` se crea un usuario con email `admin@crm.com`, rol `admin` y estado `activo`. Si ya existe, no se duplica. |

---

### SC-04: Pantalla de registro
**Como** usuario nuevo, **quiero** registrarme en el sistema con mi nombre, email y contraseña **para** obtener acceso al CRM.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimación | 3 pts |
| Criterios de aceptación | Campos: nombre (requerido), email (requerido, único, formato válido), contraseña (requerido, mínimo 8 caracteres), confirmar contraseña (debe coincidir). Validaciones en tiempo real con Livewire. Al registrar exitosamente, redirige al dashboard. Si el email ya existe, muestra error inline. El usuario se crea con rol `cliente` y estado `activo` por defecto. |

---

### SC-05: Pantalla de login
**Como** usuario registrado, **quiero** iniciar sesión con mi email y contraseña **para** acceder al sistema.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimación | 3 pts |
| Criterios de aceptación | Campos: email (requerido), contraseña (requerido). Si las credenciales son válidas y el usuario está activo, redirige al dashboard. Si las credenciales son inválidas, muestra error "Email o contraseña incorrectos". Si el usuario está inactivo, muestra error "Su cuenta está deshabilitada". Incluye enlace a la pantalla de registro. |

---

### SC-06: Layout principal y dashboard
**Como** usuario autenticado, **quiero** ver un layout con navegación y un dashboard **para** tener un punto de acceso centralizado al CRM.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimación | 3 pts |
| Criterios de aceptación | El layout incluye navbar con nombre del usuario y botón de logout. Sidebar con enlaces a los módulos (deshabilitados por ahora excepto Dashboard). El dashboard muestra un mensaje de bienvenida con el nombre del usuario. Solo accesible para usuarios autenticados (redirige a login si no lo está). El logout destruye la sesión y redirige a login. |

---

### SC-07: Middleware de roles y estado
**Como** administrador, **quiero** que el acceso a las rutas esté protegido por role y state **para** garantizar la seguridad del sistema.

| Campo | Detalle |
|---|---|
| Prioridad | Media |
| Estimación | 2 pts |
| Criterios de aceptación | El middleware `CheckRole` recibe los roles permitidos y devuelve 403 si el usuario no tiene el rol adecuado. El middleware `CheckEstado` devuelve 403 y cierra sesión si el usuario está inactivo. Ambos middleware están registrados y pueden aplicarse a grupos de rutas. |

---

## Resumen de estimación

| Story Card | Puntos |
|---|---|
| SC-01: Crear proyecto Laravel | 3 |
| SC-02: Migración de usuarios con roles | 2 |
| SC-03: Seeder de administrador | 1 |
| SC-04: Pantalla de registro | 3 |
| SC-05: Pantalla de login | 3 |
| SC-06: Layout principal y dashboard | 3 |
| SC-07: Middleware de roles y estado | 2 |
| **Total** | **17 pts** |
