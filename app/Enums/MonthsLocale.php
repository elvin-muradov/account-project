<?php

namespace App\Enums;

use App\Traits\EnumParser;

enum MonthsLocale: string
{
    use EnumParser;

    case yanvar = 'yanvar'; // Yanvar
    case fevral = 'fevral'; // Fevral
    case mart = 'mart'; // Mart
    case aprel = 'aprel'; // Aprel
    case may = 'may'; // May
    case iyun = 'iyun'; // Iyun
    case iyul = 'iyul'; // Iyul
    case avqust = 'avqust'; // Avqust
    case sentyabr = 'sentyabr'; // Sentyabr
    case oktyabr = 'oktyabr'; // Oktyabr
    case noyabr = 'noyabr'; // Noyabr
    case dekabr = 'dekabr'; // Dekabr
}
