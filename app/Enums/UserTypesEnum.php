<?php

namespace App\Enums;

use App\Traits\EnumParser;

enum UserTypesEnum: string
{
    use EnumParser;

    case LEGAL = 'LEGAL'; // Hüquqi şəxs
    case INDIVIDUAL = 'INDIVIDUAL'; // Fiziki şəxs
}
