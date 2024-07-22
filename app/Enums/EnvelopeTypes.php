<?php

namespace App\Enums;

use App\Traits\EnumParser;

enum EnvelopeTypes: string
{
    use EnumParser;

    case INCOMING = "INCOMING"; // Gələn
    case OUTGOING = "OUTGOING"; // Gedən

    public function getLabel(): string
    {
        return match ($this) {
            self::INCOMING => 'Gələn',
            self::OUTGOING => 'Gedən'
        };
    }
}
