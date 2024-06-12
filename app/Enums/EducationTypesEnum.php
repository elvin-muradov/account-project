<?php

namespace App\Enums;

use App\Traits\EnumParser;

enum EducationTypesEnum: string
{
    use EnumParser;

    case SECONDARY = 'SECONDARY'; // Orta təhsil
    case HIGH = 'HIGH'; // Ali təhsil
    case COMPLETED_HIGHER = 'COMPLETED_HIGHER'; // Tam ali təhsil
    case NULL = 'NULL'; // Boş
}
