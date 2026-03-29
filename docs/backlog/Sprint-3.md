# Sprint 3 - Gestión de Productos y Categorías

**Módulo:** Gestión de Productos  
**Duración estimada:** 2 semanas  
**Objetivo:** Implementar el ABM completo de productos y tipos de categoría, permitiendo al equipo comercial crear, consultar, editar y dar de baja productos desde la interfaz web.

---

## Alcance del Sprint

### Vistas - Blade + Livewire (Product)
- Componente Livewire de listado de productos con tabla paginada.
- Barra de búsqueda reactiva y filtros (por nombre, versión, categoría y estado) con Livewire.
- Formulario Blade de alta de producto con validaciones en tiempo real (Livewire).
- Formulario de edición de producto (reutilización del formulario de alta).
- Modal de confirmación de baja lógica de producto.
- Notificaciones flash de éxito/error en cada operación.

### Vistas - Blade + Livewire (TipoCategoria)
- Componente Livewire de listado de categorías con tabla paginada.
- Formulario Blade de alta/edición de categoría con validaciones en tiempo real.
- Modal de confirmación de baja lógica de categoría.
- Notificaciones flash de éxito/error.

### Backend - Laravel (Product + TipoCategoria)
- **Modelo Eloquent** `Product` con campos: id, category_id, nombre, descripcion, unit_price, estado, created_at, updated_at, deleted_at (SoftDeletes).
- **Modelo Eloquent** `Category` con campos: id, nombre, descripcion, state, created_at, updated_at.
- **Migración** de creación de tabla `products` en SQLite.
- **Migración** de creación de tabla `category` en SQLite.
- **Rutas web** (sin API REST, ya que Livewire maneja las acciones):
  - `GET /products` - Listado (Livewire).
  - `GET /products/create` - Formulario de alta.
  - `GET /products/{product}/edit` - Formulario de edición.
  - `GET /categories` - Listado (Livewire).
  - `GET /categories/create` - Formulario de alta.
  - `GET /categories/{category}/edit` - Formulario de edición.
- **Actions Livewire** para persistencia y baja lógica:
  - `InsertProduct`, `UpdateProduct`, `DeactivateProduct`, `ActivateProduct`.
  - `InsertCategory`, `UpdateCategory`, `DeactivateCategory`.

### Pruebas
- Tests unitarios de validaciones del modelo `Product` y `Category`.
- Tests feature para operaciones de producto (store, update, activate, deactivate).
- Tests feature para operaciones de categoría (store, update, deactivate).
- Tests de componente Livewire (búsqueda, filtros, paginación y combinación de filtros).
- Test E2E con Laravel Dusk: flujo completo alta de categoría -> alta de producto -> listado -> edición -> baja lógica.

---

## Story Cards

### SC-01: Listar productos
**Como** comercial, **quiero** ver una lista paginada de productos **para** tener una vista general del catálogo.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimación | 3 pts |
| Criterios de aceptación | La tabla muestra nombre, versión, categoría y estado. Se pagina de a 10 registros. Si no hay productos, muestra mensaje "No hay productos registrados". |

**Checklist de subtareas (SC-01)**
- [x] Crear componente Livewire para listado de productos.
- [x] Implementar consulta con paginación de 10 registros.
- [x] Construir tabla con columnas: nombre, versión, categoría y estado.
- [x] Mostrar mensaje "No hay productos registrados" cuando no existan datos.
- [x] Definir ruta `GET /products` para renderizar el listado.
- [x] Crear test de listado con paginación y estado vacío.

---

### SC-02: Buscar y filtrar productos
**Como** comercial, **quiero** buscar productos y aplicar filtros por categoría y estado **para** encontrar rápidamente información del catálogo.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimación | 2 pts |
| Criterios de aceptación | La búsqueda filtra en tiempo real al escribir (mínimo 3 caracteres). El filtro de estado permite "available" / "out_of_stock" / "discontinued". El filtro de categoría permite seleccionar una categoría o "Todas". Los filtros se combinan entre sí. |

**Checklist de subtareas (SC-02)**
- [x] Agregar input de búsqueda reactiva en el listado de productos.
- [x] Implementar filtro por nombre con mínimo de 3 caracteres.
- [x] Agregar selector de categoría con opción "Todas".
- [x] Agregar selector de estado con opciones Activo, Inactivo y Todos.
- [x] Combinar búsqueda y filtros en una sola consulta.
- [x] Reiniciar paginación al cambiar búsqueda o filtros.
- [x] Crear tests Livewire para búsqueda, filtro y combinación de filtros.

---

### SC-03: Agregar producto
**Como** comercial, **quiero** registrar un nuevo producto **para** incorporarlo al catálogo.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimación | 5 pts |
| Criterios de aceptación | Campos: categoría (requerido), nombre (requerido), descripción (opcional), precio unitario (numérico, opcional, mínimo 0). Al guardar exitosamente, redirige al listado con mensaje de confirmación. El nombre debe ser único por categoría. |

**Checklist de subtareas (SC-03)**
- [x] Crear vista/formulario Livewire para alta de producto.
- [x] Definir reglas de validación (requeridos y unicidad nombre por categoría).
- [x] Validar `unit_price` como valor numérico mayor o igual a 0.
- [x] Implementar guardado de producto en base de datos.
- [x] Manejar errores de validación mostrando mensajes por campo.
- [x] Redirigir al listado con mensaje flash de confirmación al crear.
- [x] Crear test feature para store exitoso y store con validación fallida.

---

### SC-04: Editar producto
**Como** comercial, **quiero** modificar los datos de un producto existente **para** mantener actualizado el catálogo.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimación | 3 pts |
| Criterios de aceptación | Al seleccionar "Editar" en el listado, se abre el formulario con los datos precargados. Se aplican las mismas validaciones que en alta. Al guardar, redirige al listado con mensaje de confirmación. |

**Checklist de subtareas (SC-04)**
- [x] Agregar acción "Editar" en el listado de productos.
- [x] Crear vista/formulario de edición reutilizando formulario de alta.
- [x] Cargar datos del producto seleccionado en el formulario.
- [x] Reutilizar validaciones de alta ajustando unicidad al actualizar.
- [x] Guardar cambios y redirigir al listado con mensaje flash de confirmación.
- [x] Crear test feature para update exitoso y validaciones.

---

### SC-05: Dar de baja producto
**Como** comercial, **quiero** dar de baja un producto **para** que no aparezca en el listado por defecto.

| Campo | Detalle |
|---|---|
| Prioridad | Media |
| Estimación | 2 pts |
| Criterios de aceptación | Al presionar "Eliminar" se muestra un modal de confirmación. La baja es lógica (cambia estado a "out_of_stock"). El producto permanece visible con filtro "out_of_stock" pero no aparece en el listado por defecto. Si se lo elimina en el estado "out_of_stock" pasa al estado "Descontinuado". |

**Checklist de subtareas (SC-05)**
- [x] Agregar acción "Eliminar" en el listado de productos.
- [x] Implementar modal de confirmación de baja lógica.
- [x] Implementar baja lógica cambiando estado a "out_of_stock".
- [x] Ajustar listado por defecto para mostrar productos activos.
- [x] Verificar que producto out_of_stock aparezca al usar filtro "out_of_stock".
- [x] Mostrar notificación flash de confirmación al desactivar.
- [x] Crear test feature/livewire para flujo de baja lógica.

---

### SC-06: Configurar base de datos de productos
**Como** desarrollador, **quiero** crear la migración de la tabla `products` en SQLite **para** tener la persistencia del catálogo lista.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimación | 2 pts |
| Criterios de aceptación | La migración crea la tabla con todos los campos del modelo Product (incluyendo `unit_price`). Incluye FK a `categoria_producto`, índice compuesto para unicidad (`categoria_id`, `nombre`), índice en `estado` y soporte de SoftDeletes. Se puede ejecutar (`php artisan migrate`) y revertir (`php artisan migrate:rollback`) sin errores. |

**Checklist de subtareas (SC-06)**
- [x] Crear migración de tabla `products` con campos del modelo.
- [x] Definir FK a `categoria`.
- [x] Definir índice único compuesto (`categoria_id`, `nombre`).
- [x] Definir índice para `estado`.
- [x] Incluir `softDeletes()` en la migración.
- [x] Ejecutar `php artisan migrate` y verificar creación correcta.
- [x] Ejecutar `php artisan migrate:rollback` y verificar reversión sin errores.

---

### SC-07: Gestionar categorías de producto
**Como** comercial, **quiero** administrar categorías de producto **para** clasificar el catálogo de forma consistente.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimación | 3 pts |
| Criterios de aceptación | Se permite crear, editar y dar de baja categorías. Campos: nombre (requerido, único), descripción (opcional). No se permite desactivar una categoría si tiene productos activos asociados. |

**Checklist de subtareas (SC-07)**
- [x] Crear vista/formulario Livewire para lista de categorias.
- [x] Crear formulario Livewire de alta/edición de categoría.
- [x] Implementar validaciones de categoría (nombre único, requerido).
- [x] Implementar baja con regla de negocio por productos activos asociados.
- [x] Mostrar mensajes de error/éxito por operación.
- [x] Crear tests feature/livewire del ABM de categorías.

---

### SC-08: Configurar base de datos de categorías
**Como** desarrollador, **quiero** crear la migración de la tabla `categories` en SQLite **para** soportar la clasificación del catálogo.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimación | 2 pts |
| Criterios de aceptación | La migración crea la tabla con campos: id, name, descripcion, state, timestamps, deleted_at. Incluye índice único en `name`. Se puede ejecutar y revertir sin errores. |

**Checklist de subtareas (SC-08)**
- [x] Crear migración de tabla `categories`.
- [x] Definir índice único para `name`.
- [x] Ejecutar `php artisan migrate` y verificar creación correcta.
- [x] Ejecutar `php artisan migrate:rollback` y verificar reversión sin errores.

---

### SC-09: Completar cobertura de pruebas de Sprint 3
**Como** equipo de desarrollo, **quiero** cerrar la cobertura de pruebas faltante **para** asegurar el criterio de calidad y evitar regresiones en ABM de productos y categorías.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimación | 5 pts |
| Criterios de aceptación | Existen tests automáticos para activación de productos y flujo positivo de baja de categoría sin productos asociados. Se documenta explícitamente que en categorías no aplica operación de activación porque la baja es física, y se deja preparado el escenario E2E de Dusk para el flujo crítico del sprint. |

**Checklist de subtareas (SC-09)**
- [x] Relevar cobertura actual de tests de productos/categorías y detectar brechas.
- [x] Crear test feature para operación `activate` de producto.
- [x] Agregar test feature para baja lógica exitosa de categoría sin productos asociados.
- [x] Documentar que no aplica operación `activate` en categorías por baja física.
- [x] Configurar Laravel Dusk
- [x] Crear test E2E del flujo: alta categoría -> alta producto -> listado -> edición -> baja lógica.

---

## Resumen de estimación

| Story Card | Puntos |
|---|---|
| SC-01: Listar productos | 3 |
| SC-02: Buscar y filtrar productos | 2 |
| SC-03: Crear producto | 5 |
| SC-04: Editar producto | 3 |
| SC-05: Dar de baja producto | 2 |
| SC-06: Configurar base de datos de productos | 2 |
| SC-07: Gestionar categorías de producto | 3 |
| SC-08: Configurar base de datos de categorías | 2 |
| SC-09: Completar cobertura de pruebas de Sprint | 2 |
| **Total** | **24 pts** |
