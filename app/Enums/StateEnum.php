<?php

namespace App\Enums;

use App\Traits\HasEnumHelpers;

enum StateEnum : string
{
    use HasEnumHelpers;

    case ACTIVE = 'activo';
    case INACTIVE = 'inactivo';
}
