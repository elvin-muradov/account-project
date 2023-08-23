<?php

namespace App\Traits;

trait EnumParser
{
    public static function toArray(): array
    {
        $values = [];

        foreach (self::cases() as $val) {
            $values[$val->name] = $val->value;
        }

        return $values;
    }

    public static function toString(): string
    {
        return implode(',', self::toArray());
    }
}
