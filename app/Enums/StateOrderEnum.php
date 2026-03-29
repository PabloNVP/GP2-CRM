<?php

namespace App\Enums;

use App\Traits\HasEnumHelpers;

enum StateOrderEnum : string
{
    use HasEnumHelpers;

    case PENDING = 'Pendiente';          # El pedido ha sido recibido pero aún no se ha procesado.
    case PROCESSING = 'En proceso';      # El pedido está siendo preparado o empaquetado.
    case SHIPPED = 'Enviado';            # El pedido ha sido enviado al cliente.
    case DELIVERED = 'Entregado';        # El pedido ha sido entregado al cliente.
    case CANCELLED = 'Cancelado';        # El pedido ha sido cancelado por el cliente o por la tienda.
    case RETURNED = 'Devuelto';          # El pedido ha sido devuelto por el cliente después de la entrega.
} 