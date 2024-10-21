<?php

namespace App\Enums;

use App\Traits\EnumParser;

enum EmployeeTypes: string
{
    use EnumParser;

    case EMPLOYEE = "EMPLOYEE"; // İşçi
    case DIRECTOR = "DIRECTOR"; // Direktor
    case FOUNDER = "FOUNDER"; // Təsisçi
}
