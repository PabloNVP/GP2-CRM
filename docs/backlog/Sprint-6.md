# Sprint 6 - Gestion de Soporte (Tickets)

**Modulo:** Gestion de Soporte  
**Duracion estimada:** 2 semanas  
**Objetivo:** Implementar el flujo de tickets de soporte de punta a punta, permitiendo crear, listar, filtrar, responder y cerrar tickets con trazabilidad completa.

**Nota:** Este sprint cubre la gestion de tickets y sus respuestas. La integracion con notificaciones externas (email/whatsapp) queda fuera de alcance.

---

## Alcance del Sprint

### Vistas - Blade + Livewire (Ticket)
- Componente Livewire de listado de tickets con tabla paginada.
- Barra de busqueda reactiva y filtros (por cliente, estado, prioridad, producto y fecha).
- Formulario Livewire para crear ticket.
- Vista de detalle de ticket con timeline de respuestas.
- Acciones para cambiar estado y prioridad del ticket.
- Formulario de respuesta rapida dentro del detalle.
- Notificaciones flash de exito/error en cada operacion.

### Backend - Laravel (Ticket + TicketResponse)
- **Modelo Eloquent** `Ticket` con campos: id, client_id, product_id, subject, description, priority, state, created_at, updated_at, deleted_at (SoftDeletes).
- **Modelo Eloquent** `TicketResponse` con campos: id, ticket_id, user_id, message, created_at, updated_at.
- **Migracion** de creacion de tabla `tickets` en SQLite.
- **Migracion** de creacion de tabla `ticket_responses` en SQLite.
- **Rutas web** (sin API REST, ya que Livewire maneja las acciones):
  - `GET /tickets` - Listado (Livewire).
  - `GET /tickets/create` - Formulario de alta.
  - `GET /tickets/{ticket}` - Detalle del ticket.
- **Actions Livewire** para persistencia y transiciones:
  - `CreateTicket`, `ListTickets`, `AddTicketResponse`, `ChangeTicketState`, `ChangeTicketPriority`.
- Validacion de reglas de negocio:
  - Solo se permite crear tickets para clientes activos.
  - `product_id` es opcional, pero si se informa debe existir y estar activo.
  - Transiciones permitidas: `abierto -> en_progreso -> resuelto -> cerrado`.
  - Se permite reapertura: `resuelto/cerrado -> en_progreso`.
  - No se puede cerrar un ticket sin al menos una respuesta de soporte.

### Pruebas
- Tests unitarios para reglas de transicion de estado y prioridad.
- Tests feature para alta, visualizacion, respuesta, cambio de estado y reapertura.
- Tests Livewire para listado, filtros y acciones sobre tickets.
- Test E2E con Laravel Dusk: alta ticket -> respuesta soporte -> resolucion -> cierre.

---

## Story Cards

### SC-01: Configurar base de datos de tickets
**Como** desarrollador, **quiero** crear las tablas `tickets` y `ticket_responses` **para** tener persistencia del modulo de soporte.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimacion | 4 pts |
| Criterios de aceptacion | Las migraciones crean ambas tablas con FK a clientes, productos y usuarios, con indices en `state`, `priority` y `created_at` para tickets. Incluye SoftDeletes en `tickets`. Los campos `state` y `priority` se definen mediante enums de dominio. Se pueden ejecutar y revertir sin errores. |

**Checklist de subtareas (SC-01)**
- [x] Crear migracion de tabla `tickets`.
- [x] Crear migracion de tabla `ticket_responses`.
- [x] Definir claves foraneas e indices requeridos.
- [x] Crear enum `PriorityTicketEnum` para prioridades de ticket.
- [x] Crear enum `StateTicketEnum` para estados de ticket.
- [x] Actualizar migracion de `tickets` para usar enums (`getDatabaseValues` y `getDefaultValue`).
- [x] Ejecutar `php artisan migrate` y validar estructura.
- [x] Ejecutar `php artisan migrate:rollback` y validar reversion.

---

### SC-02: Listar tickets
**Como** soporte, **quiero** ver una lista paginada de tickets **para** priorizar y atender solicitudes rapidamente.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimacion | 3 pts |
| Criterios de aceptacion | La tabla muestra numero/id de ticket, cliente, producto (si aplica), prioridad, estado y fecha de creacion. Se pagina de a 10 registros. Si no hay tickets, se muestra "No hay tickets registrados". |

**Checklist de subtareas (SC-02)**
- [x] Crear componente Livewire de listado de tickets.
- [x] Implementar consulta paginada con relaciones de cliente y producto.
- [x] Construir tabla con columnas clave de negocio.
- [x] Mostrar estado vacio cuando no existan registros.
- [x] Definir ruta `GET /tickets` para renderizar el listado.
- [x] Crear test feature/livewire de listado y paginacion.

**Implementacion realizada (31/03/2026)**
- Se agrego la accion `ListingTicket` y el componente `IndexTickets` para listar tickets paginados de a 10.
- Se incorporo la vista `tickets.index-tickets` con columnas: ticket, cliente, producto, prioridad, estado y fecha de creacion.
- Se agrego la ruta `GET /tickets` (`tickets.index`) y se conecto el acceso desde la navegacion lateral.
- Se creo cobertura automatizada en `TicketsListingTest` para estado vacio, columnas, paginacion, ruta autenticada y redireccion de guest.

---

### SC-03: Buscar y filtrar tickets
**Como** soporte, **quiero** buscar y filtrar tickets por multiples criterios **para** encontrar casos urgentes en menor tiempo.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimacion | 2 pts |
| Criterios de aceptacion | La busqueda filtra por asunto, cliente o id de ticket. Existen filtros por prioridad, estado, producto y rango de fecha. Los filtros se pueden combinar y reinician la paginacion al cambiar. |

**Checklist de subtareas (SC-03)**
- [x] Agregar input de busqueda reactiva.
- [x] Implementar filtro por asunto, cliente e id.
- [x] Agregar filtros por prioridad, estado, producto y fecha.
- [x] Combinar filtros en una sola consulta.
- [x] Reiniciar paginacion al cambiar filtros.
- [x] Crear tests Livewire para filtros combinables.

**Implementacion realizada (31/03/2026)**
- Se extendio `ListingTicket` para soportar filtros combinables por `search`, `priority`, `state`, `product_id`, `fromDate` y `toDate`.
- Se actualizo `IndexTickets` con propiedades de filtros y `resetPage()` automatico al modificar cada criterio.
- Se agrego UI de filtros en `tickets.index-tickets` (busqueda reactiva, selects y rango de fechas).
- Se incorporo columna `Asunto` en el listado para visibilidad del campo filtrado.
- Se creo cobertura automatizada en `TicketsFiltersTest` para filtros individuales, combinacion de filtros y reset de paginacion.

---

### SC-04: Crear ticket de soporte
**Como** operador, **quiero** registrar un ticket con el detalle del problema **para** iniciar el circuito de soporte.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimacion | 4 pts |
| Criterios de aceptacion | Campos: cliente (requerido), producto (opcional), asunto (requerido), descripcion (requerido), prioridad (requerido, default: media). Al guardar, crea ticket en estado `abierto` y redirige al detalle con mensaje de confirmacion. |

**Checklist de subtareas (SC-04)**
- [x] Crear formulario Livewire de alta de ticket.
- [x] Implementar validaciones de campos requeridos y relaciones.
- [x] Validar que el cliente este activo.
- [x] Persistir ticket con estado inicial `abierto`.
- [x] Redirigir al detalle con mensaje flash de exito.
- [x] Crear test feature para alta exitosa y validaciones fallidas.

**Implementacion realizada (31/03/2026)**
- Se agrego el componente `AddTicket` con formulario Livewire para alta y validaciones de negocio.
- Se implemento la accion `CreateTicket` para persistir tickets y devolver la entidad creada.
- Se validaron reglas de dominio: cliente activo obligatorio y producto opcional solo si esta disponible.
- Se agregaron rutas `tickets.create` y `tickets.show`, y flujo de redireccion post-guardado al detalle del ticket.
- Se incorporo acceso desde listado con boton `Agregar ticket` y accion `Ver detalle` por fila.
- Se creo cobertura automatizada en `TicketStoreTest` para alta exitosa y escenarios de validacion fallida.

---

### SC-05: Ver detalle y trazabilidad del ticket
**Como** soporte, **quiero** ver el historial completo del ticket **para** entender contexto y seguimiento de la incidencia.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimacion | 2 pts |
| Criterios de aceptacion | La vista de detalle muestra datos del ticket, cliente, producto, estado, prioridad y timeline de respuestas ordenadas por fecha. Si el ticket no existe, retorna 404. |

**Checklist de subtareas (SC-05)**
- [x] Crear vista Livewire/Blade de detalle de ticket.
- [x] Cargar relaciones necesarias de forma eficiente.
- [x] Mostrar timeline cronologico de respuestas.
- [x] Manejar caso inexistente con respuesta 404.
- [x] Crear test feature de visualizacion y 404.

**Implementacion realizada (31/03/2026)**
- Se implemento `ShowTicket` con route model binding y carga eficiente de relaciones (`client`, `product`, `responses`, `responses.user`).
- La timeline de respuestas se muestra en orden cronologico por `created_at`.
- Se incorporo vista de detalle con datos completos del ticket, cliente, producto, estado, prioridad y descripcion.
- Se agrego cobertura automatizada en `TicketShowTest` para visualizacion completa y respuesta 404 cuando el ticket no existe.

---

### SC-06: Responder tickets de soporte
**Como** soporte, **quiero** registrar respuestas en un ticket **para** documentar acciones y comunicar avances.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimacion | 3 pts |
| Criterios de aceptacion | Un usuario autenticado con rol de soporte/admin puede agregar respuestas no vacias al ticket. Cada respuesta guarda autor y fecha. Al responder, el ticket pasa a `en_progreso` si estaba en `abierto`. |

**Checklist de subtareas (SC-06)**
- [x] Crear formulario de respuesta en el detalle del ticket.
- [x] Implementar accion `AddTicketResponse`.
- [x] Guardar autor (`user_id`) y timestamp de respuesta.
- [x] Actualizar estado a `en_progreso` cuando corresponda.
- [x] Mostrar respuestas nuevas sin recargar pagina completa.
- [x] Crear tests feature/livewire de respuesta y autorizacion por rol.

**Implementacion realizada (31/03/2026)**
- Se implemento la accion `AddTicketResponse` con transaccion para registrar respuesta y actualizar estado del ticket de `abierto` a `en_progreso` cuando aplica.
- Se agrego formulario de respuesta en `show-ticket` y validacion de mensaje no vacio.
- Se incorporo control de autorizacion por rol en el componente: solo `soporte` y `administrador` pueden responder.
- Se guarda `user_id` del usuario autenticado y se actualiza el timeline de respuestas en la misma vista sin recarga completa.
- Se agregaron pruebas automatizadas en `TicketResponseTest` para flujo soporte/admin, bloqueo de operador y validacion de mensaje requerido.

---

### SC-07: Gestionar estado y prioridad del ticket
**Como** soporte, **quiero** actualizar estado y prioridad **para** reflejar correctamente el ciclo de atencion.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimacion | 3 pts |
| Criterios de aceptacion | Se permiten transiciones validas de estado y reapertura segun reglas definidas. Se puede ajustar prioridad (baja/media/alta/critica). Se informa mensaje de exito/error y se actualiza listado/detalle. |

**Checklist de subtareas (SC-07)**
- [x] Implementar accion `ChangeTicketState`.
- [x] Implementar accion `ChangeTicketPriority`.
- [x] Validar transiciones permitidas e invalidas.
- [x] Agregar modal de confirmacion para cierre y reapertura.
- [x] Reflejar cambios en listado y detalle en tiempo real.
- [x] Crear tests feature de transiciones y cambios de prioridad.

**Implementacion realizada (31/03/2026)**
- Se implementaron las acciones `ChangeTicketState` y `ChangeTicketPriority` con reglas de negocio y manejo de errores de dominio.
- Transiciones habilitadas: `abierto -> en_progreso -> resuelto -> cerrado`, con reapertura `resuelto/cerrado -> en_progreso`.
- Se bloqueo el cierre de tickets sin respuestas registradas.
- Se agregaron controles de estado/prioridad en listado y detalle, con feedback inmediato y mensajes de exito/error.
- Se incorporo modal de confirmacion para cierre y reapertura tanto en listado como en detalle.
- Se agrego cobertura automatizada en `TicketStateTransitionTest` para transiciones validas, invalidas, reapertura, cierre con/sin respuestas y autorizacion por rol.

---

### SC-08: Completar cobertura de pruebas de Sprint 6
**Como** equipo de desarrollo, **quiero** asegurar cobertura automatizada del flujo de tickets **para** reducir regresiones funcionales en soporte.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimacion | 3 pts |
| Criterios de aceptacion | Existen pruebas unitarias, feature y Livewire para alta, respuesta y cambios de estado/prioridad. Se incluye un flujo E2E con Dusk del circuito principal de atencion. |

**Checklist de subtareas (SC-08)**
- [x] Relevar cobertura actual y detectar brechas.
- [x] Agregar tests unitarios de reglas de estado/prioridad.
- [x] Agregar tests feature para alta, respuesta, cierre y reapertura.
- [x] Agregar tests Livewire para listado y filtros combinables.
- [x] Implementar test Dusk del flujo ticket abierto -> en progreso -> resuelto -> cerrado.

**Implementacion realizada (31/03/2026)**
- Se relevo la cobertura vigente del modulo y se cerraron brechas en pruebas unitarias y E2E.
- Se agregaron pruebas unitarias para reglas de dominio en `TicketStateRulesTest` y `TicketPriorityRulesTest`.
- Se consolidaron pruebas feature/livewire para alta (`TicketStoreTest`), respuestas (`TicketResponseTest`), listado/filtros (`TicketsListingTest`, `TicketsFiltersTest`) y transiciones (`TicketStateTransitionTest`).
- Se implemento y ejecuto prueba Dusk `TicketsFlowTest` para el flujo completo: `abierto -> en_progreso -> resuelto -> cerrado`.
- Resultado de ejecucion al cierre de SC-08: suite de tickets en verde (unit + feature + browser).

---

## Resumen de estimacion

| Story Card | Puntos |
|---|---|
| SC-01: Configurar base de datos de tickets | 4 |
| SC-02: Listar tickets | 3 |
| SC-03: Buscar y filtrar tickets | 2 |
| SC-04: Crear ticket de soporte | 4 |
| SC-05: Ver detalle y trazabilidad del ticket | 2 |
| SC-06: Responder tickets de soporte | 3 |
| SC-07: Gestionar estado y prioridad del ticket | 3 |
| SC-08: Completar cobertura de pruebas de Sprint 6 | 3 |
| **Total** | **24 pts** |
