<?php

namespace App\Enums;

use App\Traits\HasEnumHelpers;

enum StateTicketEnum : string
{
    use HasEnumHelpers;

    case OPEN = 'abierto';
    case IN_PROGRESS = 'en_progreso';
    case RESOLVED = 'resuelto';
    case CLOSED = 'cerrado';
}
