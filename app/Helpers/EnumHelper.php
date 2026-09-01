<?php
namespace App\Http\Helpers;

use App\Helpers\EnumHelper;

trait Enumhelper {
    use EnumHelper;
    
    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(
            static fn (self $permission): string => $permission->value,
            self::cases(),
        );
    }
}