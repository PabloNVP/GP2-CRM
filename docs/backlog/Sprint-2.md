# Sprint 2 - Gestión de Clientes

**Módulo:** Gestión de Clientes  
**Duración estimada:** 2 semanas  
**Objetivo:** Implementar el ABM completo de clientes, permitiendo al operador crear, consultar, editar y eliminar clientes desde la interfaz web.

---

## Alcance del Sprint

### Vistas — Blade + Livewire (Customer)
- Componente Livewire de listado de clientes con tabla paginada.
- Barra de búsqueda reactiva y filtros (por nombre, email, estado) con Livewire.
- Formulario Blade de alta de cliente con validaciones en tiempo real (Livewire).
- Formulario de edición de cliente (reutilización del formulario de alta).
- Modal de confirmación de eliminación.
- Notificaciones flash de éxito/error en cada operación.

### Backend — Laravel (Customer)
- **Modelo Eloquent** `Cliente` con campos: id, nombre, apellido, email, teléfono, empresa, dirección, estado, created_at, updated_at, deleted_at (SoftDeletes).
- **Migración** de creación de tabla `clientes` en SQLite.
- **Rutas web** (no API REST, ya que Livewire maneja las acciones):
  - `GET /clientes` — Listado (Livewire).
  - `GET /clientes/create` — Formulario de alta.
  - `GET /clientes/{cliente}/edit` — Formulario de edición.
- **Controller** `ClienteController` con métodos index, create, store, edit, update, destroy.
- **Form Request** `ClienteRequest` con validaciones (email único, campos requeridos).

### Pruebas
- Tests unitarios de validaciones del modelo `Cliente` y `ClienteRequest`.
- Tests Feature para cada ruta del controller (store, update, destroy).
- Tests de componente Livewire (búsqueda, filtros, paginación).
- Test E2E con Laravel Dusk: flujo completo alta → listado → edición → eliminación.

---

## Story Cards

### SC-01: Listar clientes
**Como** operador, **quiero** ver una lista paginada de todos los clientes **para** tener una vista general de la cartera.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimación | 3 pts |
| Criterios de aceptación | La tabla muestra nombre, email, teléfono, empresa y estado. Se pagina de a 10 registros. Si no hay clientes, muestra mensaje "No hay clientes registrados". |

---

### SC-02: Buscar y filtrar clientes
**Como** operador, **quiero** buscar clientes por nombre o email y filtrar por estado **para** encontrar rápidamente la información que necesito.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimación | 2 pts |
| Criterios de aceptación | La búsqueda filtra en tiempo real al escribir (mínimo 3 caracteres). El filtro de estado permite seleccionar "Activo" / "Inactivo" / "Todos". Los filtros se combinan entre sí. |

---

### SC-03: Crear cliente
**Como** operador, **quiero** registrar un nuevo cliente con sus datos de contacto **para** incorporarlo a la cartera de la empresa.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimación | 5 pts |
| Criterios de aceptación | Campos: nombre (requerido), apellido (requerido), email (requerido, único, formato válido), teléfono (opcional), empresa (opcional), dirección (opcional). Al guardar exitosamente, redirige al listado con mensaje de confirmación. Si el email ya existe, muestra error en el campo. |

---

### SC-04: Editar cliente
**Como** operador, **quiero** modificar los datos de un cliente existente **para** mantener la información actualizada.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimación | 3 pts |
| Criterios de aceptación | Al seleccionar "Editar" en el listado, se abre el formulario con los datos precargados. Se aplican las mismas validaciones que en alta. Al guardar, redirige al listado con mensaje de confirmación. |

---

### SC-05: Eliminar cliente
**Como** operador, **quiero** dar de baja un cliente **para** que no aparezca como activo en la cartera.

| Campo | Detalle |
|---|---|
| Prioridad | Media |
| Estimación | 2 pts |
| Criterios de aceptación | Al presionar "Eliminar" se muestra un modal de confirmación. La eliminación es lógica (cambia estado a "Inactivo"). El cliente permanece visible con filtro "Inactivo" pero no aparece en el listado por defecto. |

---

### SC-06: Configurar base de datos de clientes
**Como** desarrollador, **quiero** crear la migración de la tabla `clientes` en SQLite **para** tener la persistencia lista.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimación | 2 pts |
| Criterios de aceptación | La migración crea la tabla con todos los campos del modelo Eloquent. Incluye índice único en `email` e índice en `estado`. Soporta SoftDeletes. Se puede ejecutar (`php artisan migrate`) y revertir (`php artisan migrate:rollback`) sin errores. |

---

## Resumen de estimación

| Story Card | Puntos |
|---|---|
| SC-01: Listar clientes | 3 |
| SC-02: Buscar y filtrar clientes | 2 |
| SC-03: Crear cliente | 5 |
| SC-04: Editar cliente | 3 |
| SC-05: Eliminar cliente | 2 |
| SC-06: Configurar base de datos | 2 |
| **Total** | **17 pts** |

