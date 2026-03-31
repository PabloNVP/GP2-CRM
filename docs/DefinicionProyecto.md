# Definición del Proyecto - Customer Relationship Management (CRM)

## Alcance

Construir una aplicación web que permita gestionar y administrar el negocio de una empresa que desarrolla y comercializa software. El sistema centralizará la información de clientes, productos, pedidos y soporte en una única plataforma.

## Módulos

| Módulo                     | Descripción                                                      |
| -------------------------- | ---------------------------------------------------------------- |
| Gestión de Clientes        | ABM de clientes y su información de contacto.                    |
| Gestión de Productos       | ABM de productos y sus tipos de categoría.                       |
| Gestión de Soporte         | Visualizar y responder tickets de soporte de los clientes.       |
| Gestión de Pedidos         | Visualizar los pedidos realizados por clientes y su facturación. |

## Arquitectura

Arquitectura monolítica basada en Laravel (MVC). Las vistas se renderizan del lado del servidor con Blade y la interactividad se maneja con Livewire, sin necesidad de una API REST separada.

```
┌──────────┐     ┌──────────────────────────────────────────────────────────────┐     ┌────────┐
│          │     │                    Laravel 12                                │     │        │
│ Browser  │────▶│  Blade + Livewire  │  Controllers  │  Models  │  Migrations  │────▶│ SQLite │
│          │     │  (Vistas)          │  (Lógica)     │  (Datos) │  (Schema)    │     │        │
└──────────┘     └──────────────────────────────────────────────────────────────┘     └────────┘
```

| Capa    | Tecnología / Componentes                                                    |
| ------- | --------------------------------------------------------------------------- |
| Cliente | Browser                                                                     |
| Vistas  | Blade Templates + Livewire Components (Customer, Products, Orders, Support) |
| Lógica  | Laravel Controllers, Form Requests, Services                                |
| Datos   | Eloquent Models, Migrations                                                 |
| Storage | SQLite (Modelo Relacional)                                                  |

## Stack Tecnológico

| Capa          | Tecnología             | Notas                                               |
| ------------- | ---------------------- | --------------------------------------------------- |
| Frontend      | Blade + Livewire       | Templates server-side con reactividad sin JS custom |
| Backend       | Laravel 12             | Framework PHP, patrón MVC                           |
| Base de Datos | SQLite                 | Archivo local, sin servidor de BD separado          |
| Testing       | PHPUnit + Laravel Dusk | Tests unitarios, feature y E2E                      |

## Roles

| Rol                      | Descripción                                         |
| ------------------------ | --------------------------------------------------- |
| Operador                 | Gestiona clientes y realiza seguimiento de pedidos. |
| Soporte                  | Atiende y resuelve tickets de soporte.              |
| Comercial                | Gestiona productos y pedidos.                       |
| Administrativo           | Gestión de facturación y reportes.                  |
| Administrador de Sistema | Configuración y administración general del CRM.     |
| Cliente                  | Consulta el estado de sus pedidos y tickets.        |

## Analisis de Usuarios

### Operador: Laura Pérez

#### Características

- 35 años.
- Licenciada en Administración de Empresas.
- Casada y madre de dos hijos.
- Clase media-alta.
- Movilidad propia.
- Enfocada en el bienestar físico.
- Interesada en nuevas tecnologías.
- Participación en eventos sociales.

#### Tareas principales

- Agendar nuevos clientes.
- Actualizar información de los clientes actuales.
- Consultar lista de pedidos pendientes.
- Revisar progreso de los pedidos hasta su estado final.
- Filtrar pedidos según su estado.

## Usabilidad

### Facilidad de Aprendizaje

Se espera que el software sea utilizado en el día a día de la persona durante su trabajo. La curva de aprendizaje debe ser mínima, permitiendo un uso productivo desde las primeras sesiones.

### Eficiencia

Es crucial que el usuario pueda realizar las tareas en una franja de tiempo no muy prolongada. Las operaciones frecuentes (consultas, filtros, ABM) deben resolverse en pocos clics.

### Tasa de Errores

La cantidad de errores debe ser lo más pequeña posible para no entorpecer las tareas. El sistema debe guiar al usuario mediante validaciones y mensajes claros.

## Requisitos

### Funcionales

- ABM completo de clientes con información de contacto.
- ABM completo de productos y tipos de categoría.
- Gestión de pedidos con seguimiento de estados.
- Gestión de tickets de soporte.
- Consultas y filtros sobre todas las entidades.
- Seguimiento eficiente del ciclo de vida de pedidos y tickets.

### No Funcionales

- Interfaz intuitiva y responsiva.
- Validaciones en tiempo real en formularios.
- Guardado automático de datos en curso.
- Tiempos de respuesta menores a 2 segundos para operaciones comunes.
