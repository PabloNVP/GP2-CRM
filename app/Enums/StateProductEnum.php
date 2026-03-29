<?php

namespace App\Enums;

use App\Traits\HasEnumHelpers;

enum StateProductEnum : string
{
    use HasEnumHelpers;

    case AVAILABLE = 'Disponible';          # El producto está disponible para la venta.       
    case OUT_OF_STOCK = 'Sin stock';        # El producto no tiene stock disponible, pero se espera que vuelva a estar disponible pronto.
    case DISCONTINUED = 'Descontinuado';    # El producto ha sido descontinuado y no se espera que vuelva a estar disponible.
}
