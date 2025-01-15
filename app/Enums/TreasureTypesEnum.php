<?php

namespace App\Enums;

use App\Traits\EnumParser;

enum TreasureTypesEnum: string
{
    use EnumParser;

    case CASH = 'CASH'; // Nəğd
    case TRANSFER = 'TRANSFER'; // Bank köçürməsi
    case VAT_DEPOSIT = 'VAT_DEPOSIT'; // ƏDV Depozit
}
