<?php

namespace App\Enums;

use App\Traits\EnumParser;

enum AttendanceLogConfigDayTypes: string
{
    use EnumParser;

    case REST_DAY = 'REST_DAY'; // İstirahət günü
    case DAY_OF_CELEBRATION = 'DAY_OF_CELEBRATION'; // Bayram günü

    case ONE = '1';
    case TWO = '2';
    case THREE = '3';
    case FOUR = '4';
    case FIVE = '5';
    case SIX = '6';
    case SEVEN = '7';
    case EIGHT = '8';
    case NINE = '9';
    case TEN = '10';
    case ELEVEN = '11';
    case TWELVE = '12';
    case THIRTEEN = '13';
    case FOURTEEN = '14';
    case FIFTEEN = '15';
    case SIXTEEN = '16';
    case SEVENTEEN = '17';
    case EIGHTEEN = '18';
    case NINETEEN = '19';
    case TWENTY = '20';
    case TWENTY_ONE = '21';
    case TWENTY_TWO = '22';
    case TWENTY_THREE = '23';
    case TWENTY_FOUR = '24';
}
