<?php

namespace App\Helpers;

use BackedEnum;

trait EnumHelper
{
    /**
     * @return list<string>
     */
    public static function values(): array
    {
        /** @var class-string<BackedEnum&static> $enumClass */
        $enumClass = static::class;

        return array_map(
            static fn (BackedEnum $case): string => $case->value,
            $enumClass::cases(),
        );
    }
}
