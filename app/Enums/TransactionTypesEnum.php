<?php

namespace App\Enums;

use App\Traits\EnumParser;

enum TransactionTypesEnum: string
{
    use EnumParser;

    case INCOME = 'INCOME'; // Mədaxil
    case EXPENSE = 'EXPENSE'; // Məxaric
    case REFUND = 'REFUND'; // Geri qaytarma
}
