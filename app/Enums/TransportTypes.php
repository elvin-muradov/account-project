<?php

namespace App\Enums;

use App\Traits\EnumParser;

enum TransportTypes: string
{
    use EnumParser;

    case FCA = 'FCA';
    case FOB = 'FOB';
    case CIF = 'CIF';
    case EXW = 'EXW';
    case DAP = 'DAP';
    case DAT = 'DAT';
    case CPT = 'CPT';
    case CIP = 'CIP';
    case DDP = 'DDP';
    case FAS = 'FAS';
    case CFR = 'CFR';
}
