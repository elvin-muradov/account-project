<?php

namespace App\Enums;

use App\Traits\EnumParser;

enum ImportPaymentStatuses: string
{
    use EnumParser;

    case PAID = 'PAID'; // 100
    case PART_PAID = 'PART_PAID'; // 110
    case NOT_PAID = 'NOT_PAID'; // 120
}
