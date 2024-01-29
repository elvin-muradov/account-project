<?php

namespace App\Enums;

use App\Traits\EnumParser;

enum RentalTypes: string
{
    use EnumParser;

    case SHOP = 'SHOP'; // Mağaza
    case WAREHOUSE = 'WAREHOUSE'; // Anbar
    case VEHICLE = 'VEHICLE'; // Nəqliyyat
}
