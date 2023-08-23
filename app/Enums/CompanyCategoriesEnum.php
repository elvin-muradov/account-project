<?php

namespace App\Enums;

use App\Traits\EnumParser;

enum CompanyCategoriesEnum: string
{
    use EnumParser;

    case MICRO = 'MICRO'; // Mikro
    case JUNIOR = 'JUNIOR'; // Kiçik
    case MIDDLE = 'MIDDLE'; // Orta
    case SENIOR = 'SENIOR'; // İri
}
