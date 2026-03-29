<?php

namespace App\Enums;

use App\Traits\HasEnumHelpers;

enum StateEnum : string
{
    use HasEnumHelpers;

    case ACTIVE = 'activo';         # El cliente está activo y puede realizar pedidos.
    case INACTIVE = 'inactivo';     # El cliente está inactivo y no puede realizar pedidos.
}
