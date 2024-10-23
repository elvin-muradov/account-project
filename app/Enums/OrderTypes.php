<?php

namespace App\Enums;

use App\Traits\EnumParser;

enum OrderTypes: string
{
    use EnumParser;

    case HIRING = 'HIRING'; // İşə qəbul
    case LEAVING_WORK = 'LEAVING_WORK'; // İşdən çıxış
    case DEFAULT_HOLIDAY = 'DEFAULT_HOLIDAY'; // Adi Məzuniyyət
    case PREGNANT_HOLIDAY = 'PREGNANT_HOLIDAY'; // Hamiləlik Məzuniyyəti
    case MOTHERHOOD_HOLIDAY = 'MOTHERHOOD_HOLIDAY'; // Analıq Məzuniyyəti
    
    case BUSINESS_TRIP = 'BUSINESS_TRIP'; // Ezamiyyət
    case AWARD = 'AWARD'; // Mükafat
}
