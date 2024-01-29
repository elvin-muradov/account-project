<?php

namespace App\Enums;

use App\Traits\EnumParser;

enum GenderTypes: string
{
    use EnumParser;

    case MALE = "MALE"; // Kişi
    case FEMALE = "FEMALE"; // Qadın
}
