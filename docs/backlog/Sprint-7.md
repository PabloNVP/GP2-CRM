# Sprint 7 - Dashboard Admin y Gestion de Usuarios

**Modulo:** Administracion del Sistema  
**Duracion estimada:** 2 semanas  
**Objetivo:** Implementar un dashboard para el rol administrador que permita gestionar usuarios internos del CRM, cambiando roles y estados de forma segura y trazable.

**Nota:** Este sprint se enfoca en administracion de usuarios existentes. La recuperacion de contrasena y auditoria avanzada quedan fuera de alcance.

---

## Alcance del Sprint

### Vistas - Blade + Livewire (Admin Dashboard + Users)
- Dashboard inicial para administrador con metricas clave de usuarios.
- Componente Livewire de listado de usuarios con tabla paginada.
- Busqueda reactiva y filtros por nombre/email, rol y estado.
- Acciones rapidas por fila para cambiar rol y estado del usuario.
- Modal de confirmacion para cambios sensibles (rol/estado).
- Feedback visual y notificaciones flash de exito/error en cada operacion.

### Backend - Laravel (Users Administration)
- Reutilizacion del **Modelo Eloquent** `User` con enums de dominio:
  - `role` (`administrador`, `operador`, `soporte`, `comercial`, `administrativo`, `cliente`)
  - `state` (`activo`, `inactivo`)
- **Rutas web** (sin API REST, ya que Livewire maneja las acciones):
  - `GET /admin/dashboard` - Dashboard de administracion.
  - `GET /admin/users` - Listado y gestion de usuarios (Livewire).
- **Actions Livewire** para operaciones de administracion:
  - `ListingUsers`, `ChangeUserRole`, `ChangeUserState`.
- Validacion de reglas de negocio y seguridad:
  - Solo usuarios con rol `administrador` pueden acceder al modulo.
  - Un administrador no puede desactivarse a si mismo.
  - No se puede quitar el rol `administrador` al ultimo admin activo del sistema.
  - Solo se permiten valores definidos en `RoleEnum` y `StateEnum`.

### Pruebas
- Tests unitarios para reglas de negocio de cambio de rol/estado.
- Tests feature para autorizacion de rutas y operaciones de actualizacion.
- Tests Livewire para listado, busqueda, filtros y acciones por fila.
- Test E2E con Laravel Dusk: login admin -> dashboard -> cambio de rol -> cambio de estado.

---

## Story Cards

### SC-01: Dashboard de administracion
**Como** administrador, **quiero** visualizar un dashboard con informacion resumida de usuarios **para** supervisar rapidamente el estado del sistema.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimacion | 3 pts |
| Criterios de aceptacion | El dashboard muestra total de usuarios, usuarios activos, usuarios inactivos y distribucion por rol. Solo es accesible para rol `administrador`. |

**Checklist de subtareas (SC-01)**
- [x] Crear ruta `GET /admin/dashboard` protegida por rol `administrador`.
- [x] Implementar componente/vista con tarjetas de metricas.
- [x] Calcular contadores globales y por rol desde `users`.
- [x] Mostrar estados vacios cuando no existan usuarios.
- [x] Crear test feature de acceso autorizado/no autorizado al dashboard.

---

### SC-02: Listar usuarios en panel admin
**Como** administrador, **quiero** ver una lista paginada de usuarios **para** gestionar su acceso y responsabilidades.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimacion | 3 pts |
| Criterios de aceptacion | La tabla muestra nombre, email, rol, estado y fecha de alta. Se pagina de a 10 registros y muestra mensaje cuando no hay datos. |

**Checklist de subtareas (SC-02)**
- [x] Crear componente Livewire de listado de usuarios.
- [x] Implementar consulta paginada de 10 registros.
- [x] Construir tabla con columnas clave de negocio.
- [x] Agregar acciones por fila: cambiar rol y cambiar estado.
- [x] Crear test Livewire/feature de listado y paginacion.

---

### SC-03: Buscar y filtrar usuarios
**Como** administrador, **quiero** buscar y filtrar usuarios **para** encontrar rapidamente cuentas especificas.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimacion | 2 pts |
| Criterios de aceptacion | La busqueda filtra por nombre o email. Existen filtros por rol y estado. Los filtros se combinan y reinician paginacion al cambiar. |

**Checklist de subtareas (SC-03)**
- [x] Agregar input de busqueda reactiva por nombre/email.
- [x] Agregar select de filtro por rol con opcion "Todos".
- [x] Agregar select de filtro por estado con opcion "Todos".
- [x] Combinar filtros en una sola consulta.
- [x] Crear tests Livewire para filtros individuales y combinados.

---

### SC-04: Cambiar rol de usuario
**Como** administrador, **quiero** modificar el rol de un usuario **para** asignar responsabilidades segun su funcion.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimacion | 3 pts |
| Criterios de aceptacion | El admin puede cambiar el rol a cualquier valor valido de `RoleEnum`. Se confirma la accion con modal y se refleja en tabla sin recarga completa. |

**Checklist de subtareas (SC-04)**
- [x] Implementar accion `ChangeUserRole` con validacion de enum.
- [x] Agregar control UI para seleccionar nuevo rol por usuario.
- [x] Incorporar modal de confirmacion previo a aplicar el cambio.
- [x] Mostrar notificacion flash de exito/error.
- [x] Crear tests feature/livewire para cambios validos e invalidos.

---

### SC-05: Cambiar estado de usuario
**Como** administrador, **quiero** activar o desactivar usuarios **para** controlar el acceso al sistema.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimacion | 3 pts |
| Criterios de aceptacion | El admin puede alternar entre `activo` e `inactivo` usando valores de `StateEnum`. No puede desactivarse a si mismo. Se confirma la accion y se actualiza la vista. |

**Checklist de subtareas (SC-05)**
- [x] Implementar accion `ChangeUserState` con validacion de enum.
- [x] Bloquear regla de negocio: admin no puede desactivarse a si mismo.
- [x] Agregar accion UI de activar/desactivar con confirmacion.
- [x] Mostrar feedback visual inmediato en listado.
- [x] Crear tests feature/livewire de cambio de estado y bloqueo de auto-desactivacion.

---

### SC-06: Reglas de seguridad del modulo admin
**Como** equipo de desarrollo, **quiero** reforzar reglas de autorizacion del modulo **para** evitar cambios criticos no permitidos.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimacion | 2 pts |
| Criterios de aceptacion | Solo `administrador` accede a rutas/admin acciones. No se puede quitar rol `administrador` al ultimo admin activo. Los intentos invalidos retornan error controlado y mensaje claro. |

**Checklist de subtareas (SC-06)**
- [x] Proteger rutas `/admin/*` con middleware de rol.
- [x] Validar regla de ultimo admin activo en cambio de rol/estado.
- [x] Centralizar mensajes de error de autorizacion/regla de negocio.
- [x] Registrar pruebas feature de acceso denegado por rol.
- [x] Crear pruebas unitarias para regla de ultimo admin activo.


### SC-07: Completar cobertura de pruebas de Sprint 7
**Como** equipo de desarrollo, **quiero** asegurar cobertura automatizada del modulo admin **para** reducir regresiones en gestion de usuarios.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimacion | 4 pts |
| Criterios de aceptacion | Existen pruebas unitarias, feature y Livewire para dashboard, listado, filtros y cambios de rol/estado. Se incluye flujo E2E de administracion principal con Dusk. |

**Checklist de subtareas (SC-07)**
- [x] Relevar cobertura actual y detectar brechas del modulo admin.
- [x] Agregar pruebas unitarias de reglas de negocio (auto-desactivacion y ultimo admin).
- [x] Agregar pruebas feature para rutas protegidas y cambios de rol/estado.
- [x] Agregar pruebas Livewire para busqueda, filtros y actualizacion de tabla.
- [x] Implementar test Dusk del flujo: login admin -> gestion de usuario.

---

## Resumen de estimacion

| Story Card | Puntos |
|---|---|
| SC-01: Dashboard de administracion | 3 |
| SC-02: Listar usuarios en panel admin | 3 |
| SC-03: Buscar y filtrar usuarios | 2 |
| SC-04: Cambiar rol de usuario | 3 |
| SC-05: Cambiar estado de usuario | 3 |
| SC-06: Reglas de seguridad del modulo admin | 2 |
| SC-07: Completar cobertura de pruebas de Sprint 7 | 4 |
