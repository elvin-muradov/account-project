<?php

namespace App\Enums;

use App\Traits\EnumParser;

enum EmployeeTypes: string
{
    use EnumParser;

    case EMPLOYEE = "EMPLOYEE"; // İşçi
    case DIRECTOR = "DIRECTOR"; // Direktor
    case FOUNDER = "FOUNDER"; // Təsisçi

    public function getLabel(): string
    {
        return match ($this) {
            self::EMPLOYEE => 'İşçi',
            self::DIRECTOR => 'Direktor',
            self::FOUNDER => 'Təsisçi'
        };
    }
}
