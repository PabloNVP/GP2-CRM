<?php

namespace App\Enums;

use App\Traits\HasEnumHelpers;

enum StateInvoiceEnum : string
{
    use HasEnumHelpers;

    case ISSUED = 'Emitida';
    case PAID = 'Pagada';
    case VOIDED = 'Anulada';
}
