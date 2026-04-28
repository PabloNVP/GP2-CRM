# Sprint 9 - Responsive UI Integral

**Modulo:** Experiencia de Usuario (UX)  
**Duracion estimada:** 2 semanas  
**Objetivo:** Adaptar toda la aplicacion para uso fluido en mobile, tablet y desktop, manteniendo consistencia visual y funcional en los modulos existentes.

**Nota:** Este sprint se enfoca en responsividad y usabilidad. Rediseno de branding, nuevas funcionalidades de negocio y modo offline quedan fuera de alcance.

---

## Alcance del Sprint

### Vistas - Blade + Livewire (Responsive)
- Definicion de breakpoints y reglas comunes de layout para toda la app.
- Ajuste de navegacion principal para mobile (menu colapsable y acciones rapidas).
- Adaptacion de tablas densas a vista mobile (scroll horizontal controlado o tarjetas por fila).
- Refactor de formularios para grid responsive y campos full-width en pantallas pequenas.
- Ajustes de modales, botones y espaciados para interaccion tactil.
- Homogeneizacion visual entre modulos: clientes, productos, pedidos, facturas, tickets y admin.

### Frontend - Tailwind + componentes reutilizables
- Crear utilidades CSS/Tailwind compartidas para layout responsive.
- Estandarizar contenedores (`max-width`, `padding`, `gap`) por breakpoint.
- Definir patron reutilizable para listados (tabla desktop / alternativa mobile).
- Definir patron reutilizable para formularios de alta/edicion.
- Revisar contrastes, tamanos minimos de click y legibilidad en resoluciones chicas.

### Pruebas
- Tests feature para garantizar que vistas criticas cargan correctamente autenticadas.
- Tests Livewire para estados de componentes en layout mobile (filtros, acciones, paginacion).
- Suite Dusk con validacion visual/funcional en al menos 3 viewports:
  - Mobile (375x812)
  - Tablet (768x1024)
  - Desktop (1366x768)
- Smoke test de regresion responsive sobre flujo principal por modulo.

---

## Story Cards

### SC-01: Definir baseline responsive global
**Como** equipo de desarrollo, **quiero** establecer una base responsive comun **para** evitar comportamientos inconsistentes entre pantallas y modulos.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimacion | 3 pts |
| Criterios de aceptacion | Existen reglas globales de contenedor, espaciado y breakpoints aplicadas en layout principal. Todas las vistas heredan una estructura responsive base sin overflow horizontal no controlado. |

**Checklist de subtareas (SC-01)**
- [ ] Relevar layout actual y detectar quiebres en mobile/tablet.
- [ ] Definir breakpoints de referencia para la app.
- [ ] Ajustar `layouts/app` y wrappers globales de contenido.
- [ ] Unificar paddings/margenes por breakpoint.
- [ ] Crear test smoke de render responsive del layout autenticado.

---

### SC-02: Navegacion adaptable en mobile
**Como** usuario autenticado, **quiero** navegar comodamente desde celular **para** acceder a cualquier modulo sin friccion.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimacion | 3 pts |
| Criterios de aceptacion | El sidebar/menu se transforma en menu colapsable en mobile, conserva accesos por rol y permite abrir/cerrar sin romper la vista. |

**Checklist de subtareas (SC-02)**
- [ ] Implementar patron de menu colapsable para pantallas pequenas.
- [ ] Mantener accesos condicionados por rol en version mobile.
- [ ] Agregar overlay/cierre por click externo o boton cerrar.
- [ ] Garantizar foco visible y navegacion por teclado.
- [ ] Crear pruebas Dusk de apertura/cierre y navegacion en mobile.

---

### SC-03: Listados y tablas responsivas
**Como** operador/soporte/admin, **quiero** consultar listados en celular **para** revisar informacion sin hacer zoom ni perder datos clave.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimacion | 4 pts |
| Criterios de aceptacion | Los listados criticos (clientes, productos, pedidos, facturas, tickets, usuarios) se visualizan correctamente en mobile mediante scroll horizontal controlado o formato tarjeta. Las acciones por fila siguen disponibles. |

**Checklist de subtareas (SC-03)**
- [ ] Definir patron reutilizable para tablas en desktop.
- [ ] Definir patron alternativo para mobile (stack/card o tabla scrollable).
- [ ] Adaptar componentes de listado por modulo.
- [ ] Verificar filtros, orden y paginacion en vista mobile.
- [ ] Agregar tests Livewire/feature para listados adaptados.

---

### SC-04: Formularios y modales touch-friendly
**Como** usuario del sistema, **quiero** completar formularios y confirmar acciones desde mobile **para** operar sin errores de toque o lectura.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimacion | 3 pts |
| Criterios de aceptacion | Formularios de alta/edicion usan distribucion responsive, labels legibles y botones de accion con tamano adecuado para touch. Modales no desbordan y se pueden cerrar correctamente en mobile. |

**Checklist de subtareas (SC-04)**
- [ ] Ajustar grids de formularios a 1 columna en mobile y 2+ en desktop.
- [ ] Asegurar tamanos minimos de input/boton aptos para touch.
- [ ] Adaptar modales existentes a alto/ancho responsive.
- [ ] Revisar mensajes de validacion para que no rompan el layout.
- [ ] Crear pruebas Dusk para formularios y modales en mobile/tablet.

---

### SC-05: Adaptar modulo Clientes y Productos
**Como** operador/comercial, **quiero** gestionar clientes y productos desde cualquier dispositivo **para** trabajar en movilidad.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimacion | 3 pts |
| Criterios de aceptacion | El flujo completo de clientes y productos (listar, filtrar, crear, editar, eliminar/activar) es usable en mobile, tablet y desktop sin cortes visuales ni acciones inaccesibles. |

**Checklist de subtareas (SC-05)**
- [ ] Adaptar vistas index/create/edit de clientes.
- [ ] Adaptar vistas index/create/edit de productos.
- [ ] Verificar acciones de fila y modales de confirmacion.
- [ ] Corregir desbordes de textos largos y badges de estado.
- [ ] Agregar smoke tests por flujo principal de ambos modulos.

---

### SC-06: Adaptar modulo Pedidos, Facturas, Tickets y Admin
**Como** usuario interno, **quiero** operar los modulos transversales desde mobile/tablet **para** mantener continuidad operativa fuera de escritorio.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimacion | 3 pts |
| Criterios de aceptacion | Los flujos principales de pedidos, facturas, tickets y panel admin se mantienen funcionales en todos los viewports objetivo, incluyendo filtros, cambios de estado y detalle. |

**Checklist de subtareas (SC-06)**
- [ ] Adaptar listados y vistas detalle de pedidos/facturas.
- [ ] Adaptar listado/detalle/acciones de tickets.
- [ ] Adaptar dashboard y listado de usuarios del panel admin.
- [ ] Verificar controles de filtros y acciones de cambio de estado/rol.
- [ ] Ejecutar smoke tests de flujo principal por modulo.

---

### SC-07: Accesibilidad y performance responsive
**Como** equipo de calidad, **quiero** validar criterios minimos de accesibilidad y rendimiento en mobile **para** asegurar una experiencia estable y legible.

| Campo | Detalle |
|---|---|
| Prioridad | Media |
| Estimacion | 2 pts |
| Criterios de aceptacion | Se corrigen issues criticos de contraste, foco y legibilidad en pantallas pequenas. El tiempo de carga inicial de vistas clave se mantiene dentro del objetivo del proyecto para operaciones comunes. |

**Checklist de subtareas (SC-07)**
- [ ] Revisar contraste y foco en componentes interactivos.
- [ ] Ajustar tipografias y espaciados para legibilidad mobile.
- [ ] Reducir componentes visuales que afecten performance en celular.
- [ ] Validar metricas basicas de carga en vistas criticas.
- [ ] Documentar hallazgos y correcciones aplicadas.

---

### SC-08: Completar cobertura de pruebas de Sprint 9
**Como** equipo de desarrollo, **quiero** ampliar cobertura automatizada responsive **para** prevenir regresiones visuales/funcionales entre dispositivos.

| Campo | Detalle |
|---|---|
| Prioridad | Alta |
| Estimacion | 3 pts |
| Criterios de aceptacion | Existe una bateria de pruebas Dusk por viewport y pruebas feature/livewire para flujos sensibles en layouts adaptados. La suite de sprint queda en verde al cierre. |

**Checklist de subtareas (SC-08)**
- [ ] Relevar brechas de cobertura responsive en tests existentes.
- [ ] Agregar tests Dusk parametrizados por viewport.
- [ ] Agregar tests feature/livewire de flujos criticos en mobile.
- [ ] Ejecutar regression suite del sprint y corregir fallos.
- [ ] Consolidar evidencia de ejecucion en documentacion interna.

---

## Resumen de estimacion

| Story Card | Puntos |
|---|---|
| SC-01: Definir baseline responsive global | 3 |
| SC-02: Navegacion adaptable en mobile | 3 |
| SC-03: Listados y tablas responsivas | 4 |
| SC-04: Formularios y modales touch-friendly | 3 |
| SC-05: Adaptar modulo Clientes y Productos | 3 |
| SC-06: Adaptar modulo Pedidos, Facturas, Tickets y Admin | 3 |
| SC-07: Accesibilidad y performance responsive | 2 |
| SC-08: Completar cobertura de pruebas de Sprint 9 | 3 |
| **Total** | **24 pts** |