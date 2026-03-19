<?php

namespace App\Enums;

use App\Traits\HasEnumHelpers;

enum StateProductEnum : string
{
    use HasEnumHelpers;

    case AVAILABLE = 'available';
    case NOTAVAILABLE = 'not_available';
}
