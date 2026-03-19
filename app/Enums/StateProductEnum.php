<?php

namespace App\Enums;

use App\Traits\HasEnumHelpers;

enum StateProductEnum : string
{
    use HasEnumHelpers;

    case AVAILABLE = 'Disponible';
    case OUT_OF_STOCK = 'Sin stock';
    case DISCONTINUED = 'Descontinuado';
}
