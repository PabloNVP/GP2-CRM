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
- **Modelo Eloquent** `Product` con campos: id, tipo_categoria_id, nombre, descripcion, estado, created_at, updated_at, deleted_at (SoftDeletes).
- **Modelo Eloquent** `TipoCategoria` con campos: id, nombre, descripcion, estado, created_at, updated_at, deleted_at (SoftDeletes).
- **Migración** de creación de tabla `products` en SQLite.
- **Migración** de creación de tabla `tipos_categoria` en SQLite.
- **Rutas web** (sin API REST, ya que Livewire maneja las acciones):
  - `GET /products` - Listado (Livewire).
  - `GET /products/create` - Formulario de alta.
  - `GET /products/{product}/edit` - Formulario de edición.
  - `GET /tipos-categoria` - Listado (Livewire).
  - `GET /tipos-categoria/create` - Formulario de alta.
  - `GET /tipos-categoria/{tipoCategoria}/edit` - Formulario de edición.
- **Actions Livewire** para persistencia y baja lógica:
  - `UpsertProduct`, `DeactivateProduct`, `ActivateProduct`.
  - `UpsertTipoCategoria`, `DeactivateTipoCategoria`, `ActivateTipoCategoria`.

### Pruebas
- Tests unitarios de validaciones del modelo `Product` y `TipoCategoria`.
- Tests feature para operaciones de producto (store, update, deactivate).
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
| Criterios de aceptación | Campos: categoría (requerido), nombre (requerido), descripción (opcional). Al guardar exitosamente, redirige al listado con mensaje de confirmación. El nombre debe ser único por categoría. |

**Checklist de subtareas (SC-03)**
- [x] Crear vista/formulario Livewire para alta de producto.
- [x] Definir reglas de validación (requeridos y unicidad nombre por categoría).
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
| Criterios de aceptación | Al presionar "Eliminar" se muestra un modal de confirmación. La baja es lógica (cambia estado a "Inactivo"). El producto permanece visible con filtro "Inactivo" pero no aparece en el listado por defecto. |

**Checklist de subtareas (SC-05)**
- [ ] Agregar acción "Eliminar" en el listado de productos.
- [ ] Implementar modal de confirmación de baja lógica.
- [ ] Implementar baja lógica cambiando estado a "Inactivo".
- [ ] Ajustar listado por defecto para mostrar productos activos.
- [ ] Verificar que producto inactivo aparezca al usar filtro "Inactivo".
- [ ] Mostrar notificación flash de confirmación al desactivar.
- [ ] Crear test feature/livewire para flujo de baja lógica.

---

### SC-06: Configurar base de datos de productos
**Como** desarrollador, **quiero** crear la migración de la tabla `products` en SQLite **para** tener la persistencia del catálogo lista.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimación | 2 pts |
| Criterios de aceptación | La migración crea la tabla con todos los campos del modelo Product. Incluye FK a `tipos_categoria`, índice compuesto para unicidad (`tipo_categoria_id`, `nombre`), índice en `estado` y soporte de SoftDeletes. Se puede ejecutar (`php artisan migrate`) y revertir (`php artisan migrate:rollback`) sin errores. |

**Checklist de subtareas (SC-06)**
- [ ] Crear migración de tabla `products` con campos del modelo.
- [ ] Definir FK a `tipos_categoria`.
- [ ] Definir índice único compuesto (`tipo_categoria_id`, `nombre`).
- [ ] Definir índice para `estado`.
- [ ] Incluir `softDeletes()` en la migración.
- [ ] Ejecutar `php artisan migrate` y verificar creación correcta.
- [ ] Ejecutar `php artisan migrate:rollback` y verificar reversión sin errores.

---

### SC-07: Gestionar categorías de producto
**Como** comercial, **quiero** administrar categorías de producto **para** clasificar el catálogo de forma consistente.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimación | 3 pts |
| Criterios de aceptación | Se permite listar, crear, editar y dar de baja lógica categorías. Campos: nombre (requerido, único), descripción (opcional). No se permite desactivar una categoría si tiene productos activos asociados. |

**Checklist de subtareas (SC-07)**
- [ ] Crear componente Livewire de listado de categorías con paginación.
- [ ] Crear formulario Livewire de alta/edición de categoría.
- [ ] Implementar validaciones de categoría (nombre único, requerido).
- [ ] Implementar baja lógica con regla de negocio por productos activos asociados.
- [ ] Mostrar mensajes de error/éxito por operación.
- [ ] Crear tests feature/livewire del ABM de categorías.

---

### SC-08: Configurar base de datos de categorías
**Como** desarrollador, **quiero** crear la migración de la tabla `tipos_categoria` en SQLite **para** soportar la clasificación del catálogo.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimación | 2 pts |
| Criterios de aceptación | La migración crea la tabla con campos: id, nombre, descripcion, estado, timestamps, deleted_at. Incluye índice único en `nombre`, índice en `estado` y soporte de SoftDeletes. Se puede ejecutar y revertir sin errores. |

**Checklist de subtareas (SC-08)**
- [ ] Crear migración de tabla `tipos_categoria`.
- [ ] Definir índice único para `nombre`.
- [ ] Definir índice para `estado`.
- [ ] Incluir `softDeletes()` en la migración.
- [ ] Ejecutar `php artisan migrate` y verificar creación correcta.
- [ ] Ejecutar `php artisan migrate:rollback` y verificar reversión sin errores.

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
| **Total** | **22 pts** |
