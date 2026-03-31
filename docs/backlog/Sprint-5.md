# Sprint 5 - Gestion de Facturacion de Pedidos

**Modulo:** Gestion de Facturacion  
**Duracion estimada:** 2 semanas  
**Objetivo:** Implementar el flujo de facturacion de pedidos entregados, permitiendo emitir, consultar y actualizar el estado de facturas en una unica operacion de negocio.

**Nota:** Este sprint cubre explicitamente lo que quedo fuera de alcance en Sprint 4 sobre facturacion de pedidos.

---

## Alcance del Sprint

### Vistas - Blade + Livewire (Invoice)
- Componente Livewire de listado de facturas con tabla paginada.
- Barra de busqueda reactiva y filtros (por numero, cliente, estado y fecha de emision).
- Vista de detalle de factura con datos de cliente, pedido asociado, importes y estado.
- Acciones de negocio para registrar pago y anular factura.
- Integracion en ordenes para emitir factura desde orden entregada.
- Notificaciones flash de exito/error en cada operacion.

### Backend - Laravel (Invoice)
- **Modelo Eloquent** `Invoice` con campos: id, order_id, number, issue_date, total_amount, state, created_at, updated_at.
- **Migracion** de creacion de tabla `invoices` en SQLite.
- **Rutas web** (sin API REST, ya que Livewire maneja las acciones):
  - `GET /invoices` - Listado (Livewire).
  - `GET /invoices/{invoice}` - Detalle de factura.
- **Actions Livewire** para persistencia y transiciones:
  - `GenerateInvoice`, `ListingInvoice`, `MarkInvoiceAsPaid`, `VoidInvoice`.
- Validacion de reglas de negocio:
  - Solo se puede generar factura para ordenes en estado `Entregado`.
  - Una orden solo puede tener una factura activa.
  - El monto total de factura debe coincidir con el total de la orden.
  - No se puede marcar como pagada una factura anulada.
  - No se puede anular una factura ya pagada.

### Pruebas
- Tests unitarios para generacion de numero de factura y reglas de estado.
- Tests feature para emision, visualizacion, pago y anulacion de facturas.
- Tests Livewire para listado, filtros y acciones de estado en facturas.
- Test E2E con Laravel Dusk: orden entregada -> emitir factura -> registrar pago.

---

## Story Cards

### SC-01: Configurar base de datos de facturas
**Como** desarrollador, **quiero** crear la tabla `invoices` **para** tener persistencia de la facturacion asociada a pedidos.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimacion | 3 pts |
| Criterios de aceptacion | La migracion crea `invoices` con FK a `orders`, indice unico en `order_id`, indice unico en `number`, indice por `state` y `issue_date`. Se puede ejecutar y revertir sin errores. |

**Checklist de subtareas (SC-01)**
- [x] Crear migracion de tabla `invoices`.
- [x] Definir FK a `orders` con restriccion de integridad.
- [x] Definir indice unico para `order_id`.
- [x] Definir indice unico para `number`.
- [x] Definir indices por `state` y `issue_date`.
- [x] Ejecutar `php artisan migrate` y validar estructura.
- [x] Ejecutar `php artisan migrate:rollback` y validar reversion.

---

### SC-02: Emitir factura desde una orden entregada
**Como** administrativo, **quiero** emitir una factura a partir de una orden entregada **para** formalizar la venta.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimacion | 5 pts |
| Criterios de aceptacion | Solo se puede emitir factura si la orden esta `Entregado`. Se genera numero unico de factura, fecha de emision y monto total igual al total de la orden. No se permite duplicar factura para la misma orden. |

**Checklist de subtareas (SC-02)**
- [x] Crear accion `GenerateInvoice` con transaccion atomica.
- [x] Validar estado de orden `Entregado` previo a emitir.
- [x] Implementar generador de numero unico de factura.
- [x] Persistir factura con `issue_date`, `total_amount` y `state` inicial `Emitida`.
- [x] Bloquear emision duplicada para la misma orden.
- [x] Mostrar mensaje de exito/error en la UI de ordenes.
- [x] Crear tests feature de emision valida e invalida.

---

### SC-03: Listar y filtrar facturas
**Como** administrativo, **quiero** listar y filtrar facturas **para** localizar comprobantes rapidamente.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimacion | 3 pts |
| Criterios de aceptacion | El listado muestra numero, cliente, orden, fecha de emision, estado y monto. Se pagina de a 10. Permite filtrar por numero/cliente, estado y rango de fecha. |

**Checklist de subtareas (SC-03)**
- [x] Crear componente Livewire de listado de facturas.
- [x] Implementar consulta con relaciones de orden y cliente.
- [x] Construir tabla con columnas clave de negocio.
- [x] Agregar busqueda reactiva por numero y cliente.
- [x] Agregar filtros por estado y rango de fecha.
- [x] Reiniciar paginacion al cambiar filtros.
- [x] Crear tests Livewire/feature de filtros combinables.

---

### SC-04: Ver detalle de factura
**Como** administrativo, **quiero** ver el detalle de una factura **para** auditar su contenido antes de cierre.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimacion | 2 pts |
| Criterios de aceptacion | La vista muestra numero, fecha, estado, cliente, orden asociada y monto total. Si la factura no existe, retorna 404. |

**Checklist de subtareas (SC-04)**
- [x] Crear vista Livewire/Blade de detalle de factura.
- [x] Cargar relaciones necesarias (orden y cliente) de forma eficiente.
- [x] Mostrar encabezado de factura y resumen de importes.
- [x] Manejar caso inexistente con respuesta 404.
- [x] Crear test feature de visualizacion y 404.

---

### SC-05: Registrar pago y anular factura
**Como** administrativo, **quiero** actualizar el estado de la factura **para** reflejar su ciclo administrativo.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimacion | 4 pts |
| Criterios de aceptacion | Se permite pasar de `Emitida` a `Pagada` o `Anulada`. No se puede pagar una anulada ni anular una pagada. Se informa mensaje de confirmacion/error. |

**Checklist de subtareas (SC-05)**
- [x] Implementar accion `MarkInvoiceAsPaid`.
- [x] Implementar accion `VoidInvoice`.
- [x] Validar transiciones permitidas de estado.
- [x] Agregar modal de confirmacion para anulacion.
- [x] Reflejar estado actualizado en listado y detalle.
- [x] Crear tests feature de transiciones validas e invalidas.

---

### SC-06: Integrar facturacion con modulo de pedidos
**Como** operador, **quiero** acceder a la emision y consulta de factura desde pedidos **para** no salir del flujo operativo.

| Campo | Detalle |
|---|---|
| Prioridad | Media |
| Estimacion | 2 pts |
| Criterios de aceptacion | Desde ordenes entregadas se puede emitir factura o navegar al detalle de factura existente. El listado de ordenes refleja si la factura ya fue emitida. |

**Checklist de subtareas (SC-06)**
- [x] Agregar accion de emision en listado/detalle de ordenes entregadas.
- [x] Mostrar enlace al detalle de factura cuando exista.
- [x] Inhabilitar accion de emision si ya existe factura.
- [x] Agregar feedback visual de estado de facturacion en ordenes.
- [x] Crear test feature/livewire de integracion orden-factura.

---

### SC-07: Completar cobertura de pruebas de Sprint 5
**Como** equipo de desarrollo, **quiero** asegurar cobertura automatizada del flujo de facturacion **para** reducir regresiones funcionales.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimacion | 4 pts |
| Criterios de aceptacion | Existen pruebas unitarias, feature y Livewire para emision y cambios de estado de facturas. Se incluye un flujo E2E con Dusk del circuito principal. |

**Checklist de subtareas (SC-07)**
- [x] Relevar cobertura actual y detectar brechas de facturacion.
- [x] Agregar tests unitarios del generador de numero de factura y reglas de estado.
- [x] Agregar tests feature para emision, pago y anulacion.
- [x] Agregar tests Livewire para listado y filtros de facturas.
- [x] Implementar test Dusk del flujo orden entregada -> factura emitida -> factura pagada.

---

## Resumen de estimacion

| Story Card | Puntos |
|---|---|
| SC-01: Configurar base de datos de facturas | 3 |
| SC-02: Emitir factura desde una orden entregada | 5 |
| SC-03: Listar y filtrar facturas | 3 |
| SC-04: Ver detalle de factura | 2 |
| SC-05: Registrar pago y anular factura | 4 |
| SC-06: Integrar facturacion con modulo de pedidos | 2 |
| SC-07: Completar cobertura de pruebas de Sprint 5 | 4 |
| **Total** | **23 pts** |
