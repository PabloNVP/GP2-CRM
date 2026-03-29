# Sprint 4 - Gestion de Ordenes

**Modulo:** Gestion de Ordenes  
**Duracion estimada:** 2 semanas  
**Objetivo:** Implementar el flujo de ordenes de punta a punta, permitiendo registrar pedidos con multiples productos, calcular totales y gestionar estados del ciclo de vida.

**Nota:** Todo lo relacionado con facturacion queda fuera del alcance de este sprint y se planifica para el proximo sprint.

---

## Alcance del Sprint

### Vistas - Blade + Livewire (Order)
- Componente Livewire de listado de ordenes con tabla paginada.
- Barra de busqueda reactiva y filtros (por cliente, fecha y estado).
- Formulario Livewire para crear orden con multiples lineas de producto.
- Vista de detalle de orden con resumen de cliente, items y total.
- Acciones de cambio de estado (Pendiente, En proceso, Enviado, Entregado, Cancelado, Devuelto).
- Modal de confirmacion para cancelar orden.
- Notificaciones flash de exito/error en cada operacion.

### Backend - Laravel (Order + OrderDetail)
- **Modelo Eloquent** `Order` con campos: id, client_id, date, state, total, observations, created_at, updated_at, deleted_at (SoftDeletes).
- **Modelo Eloquent** `OrderDetail` con campos: id, order_id, product_id, count, unit_price, subtotal.
- **Migracion** de creacion de tabla `orders` en SQLite.
- **Migracion** de creacion de tabla `order_details` en SQLite.
- **Rutas web** (sin API REST, ya que Livewire maneja las acciones):
  - `GET /orders` - Listado (Livewire).
  - `GET /orders/create` - Formulario de alta.
  - `GET /orders/{order}` - Detalle de orden.
  - `GET /orders/{order}/edit` - Formulario de edicion.
- **Actions Livewire** para persistencia y transiciones:
  - `InsertOrder`, `UpdateOrder`, `CancelOrder`, `ChangeOrderState`.
  - `InsertOrderDetail`, `RecalculateOrderTotal`.
- Validacion de reglas de negocio:
  - No permitir agregar productos con estado `out_of_stock`.
  - El total de la orden debe ser igual a la suma de subtotales.
  - No permitir volver a `Pendiente` una orden ya `Enviada` o `Entregada`.

### Pruebas
- Tests unitarios para calculo de subtotal y total de orden.
- Tests feature para flujo de orden (store, update, show, cancel, change state).
- Tests de componente Livewire (busqueda, filtros, paginacion y formulario con multiples items).
- Test E2E con Laravel Dusk: flujo completo alta de orden -> cambio de estado.

---

## Story Cards

### SC-01: Configurar base de datos de ordenes
**Como** desarrollador, **quiero** crear las tablas `orders` y `order_details` **para** tener la persistencia del modulo de ordenes.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimacion | 4 pts |
| Criterios de aceptacion | Las migraciones crean las tablas con claves foraneas a cliente, producto y orden. Incluyen indices por estado y fecha en ordenes, y soporte de SoftDeletes en ordenes. Se pueden ejecutar y revertir sin errores. |

**Checklist de subtareas (SC-01)**
- [x] Crear migracion de tabla `orders`.
- [x] Crear migracion de tabla `order_details`.
- [x] Definir claves foraneas e indices necesarios.
- [x] Ejecutar `php artisan migrate` y validar estructura.
- [x] Ejecutar `php artisan migrate:rollback` y validar reversion.

---

### SC-02: Listar ordenes
**Como** operador, **quiero** ver una lista paginada de ordenes **para** tener visibilidad del estado comercial y operativo.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimacion | 3 pts |
| Criterios de aceptacion | La tabla muestra numero de orden, cliente, fecha, estado y total. Se pagina de a 10 registros. Si no hay ordenes, se muestra el mensaje "No hay ordenes registradas". |

**Checklist de subtareas (SC-02)**
- [x] Crear componente Livewire de listado de ordenes.
- [x] Implementar consulta paginada con relaciones de cliente.
- [x] Construir tabla con columnas clave de negocio.
- [x] Mostrar estado vacio cuando no existan registros.
- [x] Definir ruta `GET /orders` para renderizar el listado.
- [x] Crear test feature/livewire de listado y paginacion.

---

### SC-03: Buscar y filtrar ordenes
**Como** operador, **quiero** buscar ordenes por cliente y aplicar filtros por estado y fecha **para** localizar rapidamente pedidos especificos.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimacion | 2 pts |
| Criterios de aceptacion | La busqueda filtra por nombre de cliente o numero de orden. Existen filtros por estado y rango de fecha. Los filtros se pueden combinar y reinician la paginacion al cambiar. |

**Checklist de subtareas (SC-03)**
- [x] Agregar input de busqueda reactiva.
- [x] Implementar filtro por cliente y numero de orden.
- [x] Agregar filtro por estado.
- [x] Agregar filtro por rango de fecha.
- [x] Combinar filtros en una sola consulta.
- [x] Crear tests Livewire de busqueda y combinacion de filtros.

---

### SC-04: Crear orden con multiples items
**Como** operador, **quiero** registrar una orden con varios productos **para** consolidar la compra de un cliente en un solo pedido.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimacion | 5 pts |
| Criterios de aceptacion | Se puede seleccionar cliente, fecha y agregar multiples lineas de producto con cantidad. Cada linea calcula subtotal y la orden calcula total automaticamente. No permite productos sin stock. Al guardar, redirige al detalle con mensaje de confirmacion. |

**Checklist de subtareas (SC-04)**
- [x] Crear formulario Livewire de alta de orden.
- [x] Implementar agregacion y eliminacion dinamica de items.
- [x] Calcular subtotal por item y total general en tiempo real.
- [x] Validar cantidades, productos y disponibilidad.
- [x] Persistir orden y detalles en transaccion atomica.
- [x] Crear tests feature para alta exitosa y validaciones fallidas.

---

### SC-05: Ver detalle de orden
**Como** operador, **quiero** consultar el detalle completo de una orden **para** revisar cliente, productos y montos antes de procesarla.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimacion | 2 pts |
| Criterios de aceptacion | La vista de detalle muestra cabecera de orden, datos de cliente, lineas de producto con cantidad, precio unitario, subtotal y total. Si la orden no existe, retorna 404. |

**Checklist de subtareas (SC-05)**
- [x] Crear vista Livewire/Blade de detalle de orden.
- [x] Cargar relaciones de cliente y productos de forma eficiente.
- [x] Mostrar resumen de importes por item y total.
- [x] Manejar error de orden inexistente.
- [x] Crear test feature de visualizacion de detalle.

---

### SC-06: Gestionar estados de la orden
**Como** operador, **quiero** actualizar el estado de una orden segun su avance **para** reflejar el ciclo operativo real.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimacion | 3 pts |
| Criterios de aceptacion | Las transiciones permitidas respetan la regla de negocio definida (Pendiente -> En proceso -> Enviado -> Entregado). Se permite cancelar desde Pendiente o En proceso. Se registra mensaje de confirmacion y la lista refleja el nuevo estado. |

**Checklist de subtareas (SC-06)**
- [x] Implementar maquina de estados basica para ordenes.
- [x] Agregar acciones de cambio de estado en listado y/o detalle.
- [x] Validar transiciones invalidas con mensaje de error.
- [x] Implementar accion de cancelacion con modal de confirmacion.
- [x] Crear tests feature para transiciones validas e invalidas.

---

### SC-07: Completar cobertura de pruebas de Sprint 4
**Como** equipo de desarrollo, **quiero** asegurar cobertura automatizada del flujo critico de ordenes **para** reducir regresiones funcionales.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimacion | 4 pts |
| Criterios de aceptacion | Existen pruebas unitarias, feature y Livewire para alta de orden y transiciones de estado. Se incluye al menos un flujo E2E con Dusk del circuito principal del sprint. |

**Checklist de subtareas (SC-07)**
- [x] Relevar cobertura actual y detectar brechas.
- [x] Agregar tests unitarios de calculos de montos.
- [x] Agregar tests feature de flujo de ordenes.
- [x] Agregar tests Livewire de formulario dinamico y filtros.
- [x] Implementar test Dusk del flujo critico de punta a punta.

---

## Resumen de estimacion

| Story Card | Puntos |
|---|---|
| SC-01: Configurar base de datos de ordenes | 4 |
| SC-02: Listar ordenes | 3 |
| SC-03: Buscar y filtrar ordenes | 2 |
| SC-04: Crear orden con multiples items | 5 |
| SC-05: Ver detalle de orden | 2 |
| SC-06: Gestionar estados de la orden | 3 |
| SC-07: Completar cobertura de pruebas de Sprint 4 | 4 |
| **Total** | **23 pts** |