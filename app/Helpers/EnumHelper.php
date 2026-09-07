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

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function options(): array
    {
        /** @var class-string<BackedEnum&static> $enumClass */
        $enumClass = static::class;

        return array_map(
            static fn (BackedEnum $case): array => [
                'value' => $case->value,
                'label' => ucfirst($case->value),
            ],
            $enumClass::cases(),
        );
    }
}
