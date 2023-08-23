<?php

namespace App\Enums;

use App\Traits\EnumParser;

enum CompanyObligationsEnum: string
{
    use EnumParser;

    case SIMPLIFIED = 'SIMPLIFIED'; // Sadələşdirilmiş
    case PROFIT = 'PROFIT'; // Mənfəət
    case VAT = 'VAT'; // ƏDV
}
