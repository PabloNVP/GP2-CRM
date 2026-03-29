<?php

namespace App\Enums;

enum RoleEnum : string
{
    case ADMIN = 'administrador';               # Gestiona clientes y realiza seguimiento de pedidos.
    case CLIENT = 'cliente';                    # Consulta el estado de sus pedidos y tickets.
    case OPERATOR = 'operador';                 # Gestiona clientes y realiza seguimiento de pedidos.
    case SUPPORT = 'soporte';                   # Atiende y resuelve tickets de soporte.
    case SALES = 'comercial';                   # Gestiona productos y pedidos.
    case ADMINISTRATIVE = 'administrativo';     # Gestión de facturación y reportes.

}
 