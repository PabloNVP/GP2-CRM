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

**Checklist de subtareas (SC-01)**
- [x] Crear componente Livewire para listado de clientes.
- [x] Implementar consulta con paginación de 10 registros.
- [x] Construir tabla con columnas: nombre, email, teléfono, empresa y estado.
- [x] Mostrar mensaje "No hay clientes registrados" cuando no existan datos.
- [x] Definir ruta `GET /clientes` para renderizar el listado.
- [x] Validar manualmente escenarios con 0, 1, 10 y más de 10 clientes. [Nota: Se creo el comando clients:validate-listing]
- [x] Crear test de listado con paginación y estado vacío.

---

### SC-02: Buscar y filtrar clientes
**Como** operador, **quiero** buscar clientes por nombre o email y filtrar por estado **para** encontrar rápidamente la información que necesito.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimación | 2 pts |
| Criterios de aceptación | La búsqueda filtra en tiempo real al escribir (mínimo 3 caracteres). El filtro de estado permite seleccionar "Activo" / "Inactivo" / "Todos". Los filtros se combinan entre sí. |

**Checklist de subtareas (SC-02)**
- [x] Agregar input de búsqueda reactiva en el componente de listado.
- [x] Implementar filtro por nombre o email con mínimo de 3 caracteres.
- [x] Agregar selector de estado con opciones Activo, Inactivo y Todos.
- [x] Combinar búsqueda y filtro de estado en una sola consulta.
- [x] Reiniciar paginación al cambiar búsqueda o filtro.
- [x] Crear tests Livewire para búsqueda, filtro y combinación de filtros.

---

### SC-03: Crear cliente
**Como** operador, **quiero** registrar un nuevo cliente con sus datos de contacto **para** incorporarlo a la cartera de la empresa.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimación | 5 pts |
| Criterios de aceptación | Campos: nombre (requerido), apellido (requerido), email (requerido, único, formato válido), teléfono (opcional), empresa (opcional), dirección (opcional). Al guardar exitosamente, redirige al listado con mensaje de confirmación. Si el email ya existe, muestra error en el campo. |

**Checklist de subtareas (SC-03)**
- [x] Crear vista/formulario Livewire para alta de cliente.
- [x] Definir reglas de validación (requeridos, formato y unicidad de email).
- [x] Implementar guardado de cliente en base de datos.
- [x] Manejar errores de validación mostrando mensajes por campo.
- [x] Redirigir al listado con mensaje flash de confirmación al crear.
- [x] Crear test feature para store exitoso y store con email duplicado.

---

### SC-04: Editar cliente
**Como** operador, **quiero** modificar los datos de un cliente existente **para** mantener la información actualizada.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimación | 3 pts |
| Criterios de aceptación | Al seleccionar "Editar" en el listado, se abre el formulario con los datos precargados. Se aplican las mismas validaciones que en alta. Al guardar, redirige al listado con mensaje de confirmación. |

**Checklist de subtareas (SC-04)**
- [x] Agregar acción "Editar" en el listado de clientes.
- [x] Crear vista/formulario de edición reutilizando el formulario de alta.
- [x] Cargar datos del cliente seleccionado en el formulario.
- [x] Reutilizar validaciones de alta (ajustando unicidad de email al actualizar).
- [x] Guardar cambios y redirigir al listado con mensaje flash de confirmación.
- [x] Crear test feature para update exitoso y validaciones.

---

### SC-05: Eliminar cliente
**Como** operador, **quiero** dar de baja un cliente **para** que no aparezca como activo en la cartera.

| Campo | Detalle |
|---|---|
| Prioridad | Media |
| Estimación | 2 pts |
| Criterios de aceptación | Al presionar "Eliminar" se muestra un modal de confirmación. La eliminación es lógica (cambia estado a "Inactivo"). El cliente permanece visible con filtro "Inactivo" pero no aparece en el listado por defecto. |

**Checklist de subtareas (SC-05)**
- [x] Agregar acción "Eliminar" en el listado de clientes.
- [x] Implementar modal de confirmación de eliminación.
- [x] Implementar baja lógica cambiando estado a "Inactivo".
- [x] Ajustar listado por defecto para mostrar clientes activos.
- [x] Verificar que cliente inactivo aparezca al usar filtro "Inactivo".
- [x] Mostrar notificación flash de confirmación al eliminar.
- [x] Crear test feature/livewire para flujo de baja lógica.

---

### SC-06: Configurar base de datos de clientes
**Como** desarrollador, **quiero** crear la migración de la tabla `clientes` en SQLite **para** tener la persistencia lista.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimación | 2 pts |
| Criterios de aceptación | La migración crea la tabla con todos los campos del modelo Eloquent. Incluye índice único en `email` e índice en `estado`. Soporta SoftDeletes. Se puede ejecutar (`php artisan migrate`) y revertir (`php artisan migrate:rollback`) sin errores. |

**Checklist de subtareas (SC-06)**
- [ ] Crear migración de tabla `clientes` con campos del modelo.
- [ ] Definir índice único para `email`.
- [ ] Definir índice para `estado`.
- [ ] Incluir `softDeletes()` en la migración.
- [ ] Ejecutar `php artisan migrate` y verificar creación correcta.
- [ ] Ejecutar `php artisan migrate:rollback` y verificar reversión sin errores.

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

