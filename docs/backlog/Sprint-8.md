# Sprint 8 - Autenticacion y Autorizacion por Rol

**Modulo:** Seguridad y Control de Acceso  
**Duracion estimada:** 2 semanas  
**Objetivo:** Aplicar control de acceso por rol en modulos y acciones, usando la matriz definida como fuente unica de verdad.

**Nota:** Este sprint se enfoca en control de acceso. MFA, SSO, OAuth y auditoria avanzada quedan fuera de alcance.

---

## Alcance del Sprint

### Vistas - Blade + Livewire (Acceso por Rol)
- Navegacion dinamica por rol, ocultando modulos no permitidos.
- Acciones por fila visibles segun permisos.
- Mensajes de acceso denegado consistentes.
- Redireccion segura cuando el rol no tiene acceso.

### Backend - Laravel (Autenticacion + Autorizacion)
- Fuente unica de permisos (rol/modulo/accion) basada en la matriz documentada.
- Middlewares `auth`, `state` y `role` aplicados a rutas de modulos.
- Policies/guards reutilizables para acciones criticas (cambiar estado, facturar, responder ticket).
- Control de alcance de datos por rol (cliente solo ve datos propios).

### Matriz de permisos (insumo del sprint)
- `administrador`: acceso total.
- `operador`: clientes y pedidos.
- `comercial`: productos, categorias, clientes y pedidos.
- `soporte`: tickets y consultas necesarias para soporte.
- `administrativo`: clientes, pedidos y facturas.
- `cliente`: pedidos, facturas y tickets propios.

### Pruebas
- Tests feature de acceso por ruta y rol.
- Tests Livewire de acciones bloqueadas.
- Tests unitarios de policies/guards y reglas de alcance.
- Suite Dusk con escenarios por rol (menu, acceso y redireccion).

---

## Story Cards

### SC-01: Matriz de permisos por rol (completado)
**Como** equipo de desarrollo, **quiero** documentar una matriz explicita de permisos **para** aplicar reglas consistentes en todas las capas.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimacion | 3 pts |
| Criterios de aceptacion | La matriz por rol y accion esta versionada y alineada con negocio. |

**Checklist de subtareas (SC-01)**
- [x] Relevar permisos actuales por modulo.
- [x] Definir matriz target por rol y accion.
- [x] Alinear matriz con negocio y backlog previo.
- [x] Publicar matriz en documentacion del proyecto.
- [x] Validar consistencia entre modulos y acciones.

---

### SC-02: Permisos como codigo (fuente unica)
**Como** equipo de desarrollo, **quiero** una definicion unica de permisos **para** evitar reglas duplicadas o inconsistentes.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimacion | 3 pts |
| Criterios de aceptacion | Existe un mapa de permisos reusable (por rol, modulo y accion) accesible desde middlewares, policies y UI. |

**Checklist de subtareas (SC-02)**
- [ ] Normalizar nombres de modulos y acciones.
- [ ] Crear definicion central (config/permiso + helper o servicio).
- [ ] Agregar helpers para validar permisos por rol y accion.
- [ ] Documentar el uso de la fuente unica.

---

### SC-03: Blindar rutas por rol y estado
**Como** administrador, **quiero** que toda ruta sensible este protegida **para** impedir accesos directos no permitidos.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimacion | 3 pts |
| Criterios de aceptacion | Todas las rutas de modulos usan `auth`, `state` y `role` segun la matriz, con respuesta de acceso denegado consistente. |

**Checklist de subtareas (SC-03)**
- [ ] Auditar grupos de rutas y middlewares actuales.
- [ ] Aplicar protecciones por modulo en `routes/web.php`.
- [ ] Validar orden de middlewares (`auth` -> `state` -> `role`).
- [ ] Unificar comportamiento de acceso denegado.
- [ ] Tests feature por ruta y rol.

---

### SC-04: Guardas de acciones criticas
**Como** equipo de desarrollo, **quiero** validar permisos dentro de cada accion sensible **para** evitar bypass de UI.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimacion | 4 pts |
| Criterios de aceptacion | Acciones criticas verifican permisos antes de mutar datos (crear/editar/cambiar estado/facturar/responder). No hay mutaciones en intentos denegados. |

**Checklist de subtareas (SC-04)**
- [ ] Identificar acciones criticas por modulo.
- [ ] Implementar policies/guards reutilizables.
- [ ] Centralizar mensajes de denegacion.
- [ ] Validar que los errores no filtren informacion sensible.
- [ ] Tests unitarios de reglas comunes.

---

### SC-05: Scopes de datos por rol
**Como** responsable de seguridad funcional, **quiero** limitar el alcance de datos **para** que cada rol vea solo lo permitido.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimacion | 3 pts |
| Criterios de aceptacion | El rol cliente solo accede a sus pedidos, facturas y tickets. Soporte y administrativo ven lo necesario para operar segun matriz. |

**Checklist de subtareas (SC-05)**
- [ ] Definir reglas de alcance por rol y entidad.
- [ ] Aplicar scopes/queries en listados y detalles.
- [ ] Validar acceso por URL directa a registros ajenos.
- [ ] Tests feature de acceso permitido/denegado por alcance.

---

### SC-06: UI condicionada por permisos
**Como** usuario autenticado, **quiero** ver solo opciones disponibles **para** evitar confusion y errores de acceso.

| Campo | Detalle |
|---|---|
| Prioridad | Media |
| Estimacion | 2 pts |
| Criterios de aceptacion | Sidebar, acciones por fila y botones se muestran/ocultan segun permisos usando la fuente unica. |

**Checklist de subtareas (SC-06)**
- [ ] Ajustar menu lateral por rol.
- [ ] Ocultar acciones por fila y botones no permitidos.
- [ ] Alinear visibilidad con la fuente unica de permisos.
- [ ] Verificar consistencia en desktop y mobile.

---

### SC-07: Endurecer autenticacion operativa
**Como** equipo de seguridad, **quiero** reforzar controles de sesion y estado de usuario **para** minimizar accesos indebidos en operacion diaria.

| Campo | Detalle |
|---|---|
| Prioridad | Media |
| Estimacion | 2 pts |
| Criterios de aceptacion | Usuarios inactivos no pueden operar aun con sesion previa y los flujos de login/logout mantienen comportamiento consistente. |

**Checklist de subtareas (SC-07)**
- [ ] Verificar middleware de estado en todos los modulos.
- [ ] Confirmar invalidacion de sesion ante bloqueo de usuario.
- [ ] Homologar mensajes para login denegado por estado.

---

### SC-08: Cobertura de seguridad del sprint
**Como** equipo de desarrollo, **quiero** cerrar cobertura automatizada por rol **para** prevenir regresiones de acceso.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimacion | 3 pts |
| Criterios de aceptacion | Tests feature, Livewire y unitarios cubren rutas, acciones y scopes por rol. La suite del sprint queda en verde. |

**Checklist de subtareas (SC-08)**
- [ ] Relevar brechas de cobertura de autorizacion por rol.
- [ ] Agregar tests feature parametrizados por rol/modulo.
- [ ] Agregar tests Livewire de bloqueo de acciones.
- [ ] Agregar tests unitarios de guards/policies.
- [ ] Consolidar evidencia de ejecucion y resultados.

---

## Resumen de estimacion

| Story Card | Puntos |
|---|---|
| SC-01: Matriz de permisos por rol (completado) | 3 |
| SC-02: Permisos como codigo | 3 |
| SC-03: Blindar rutas por rol y estado | 3 |
| SC-04: Guardas de acciones criticas | 4 |
| SC-05: Scopes de datos por rol | 3 |
| SC-06: UI condicionada por permisos | 2 |
| SC-07: Endurecer autenticacion operativa | 2 |
| SC-08: Cobertura de seguridad del sprint | 3 |
| **Total** | **23 pts** |
