<?php

namespace App\Enums;

use App\Traits\EnumParser;

enum AttendanceLogDayTypes: string
{
    use EnumParser;

    case REST_DAY = 'REST_DAY'; // İstirahət günü
    case NULL_DAY = 'NULL_DAY'; // İşsizlik günü
    case DAY_OF_CELEBRATION = 'DAY_OF_CELEBRATION'; // Bayram günü
    case LEAVING_WORK = 'LEAVING_WORK'; // İşdən çıxış
    case ILLNESS = 'ILLNESS'; // Xəstəlik
    case BUSINESS_TRIP = 'BUSINESS_TRIP'; // Ezamiyyət
    case DEFAULT_HOLIDAY = 'DEFAULT_HOLIDAY'; // Məzuniyyət
}
