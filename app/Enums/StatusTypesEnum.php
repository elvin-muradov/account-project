<?php

namespace App\Enums;

use App\Traits\EnumParser;

enum StatusTypesEnum: string
{
    use EnumParser;

    case APPROVED = 'APPROVED'; // Təsdiqlənmiş
    case REJECTED = 'REJECTED'; // Rədd edilmiş
    case PENDING = 'PENDING'; // Gözləmədə olan
}
