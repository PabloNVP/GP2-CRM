<?php

namespace App\Enums;

use App\Traits\HasEnumHelpers;

enum PriorityTicketEnum : string
{
    use HasEnumHelpers;

    case MEDIUM = 'media';
    case LOW = 'baja';
    case HIGH = 'alta';
    case CRITICAL = 'critica';
}
