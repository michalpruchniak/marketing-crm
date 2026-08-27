<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Manager = 'manager';
    case Coordinator = 'coordinator';
    case Developer = 'developer';
    case Sales = 'sales';
    case Marketer = 'marketer';
    case Viewer = 'viewer';

    /**
     * Roles that may be assigned as a client coordinator.
     *
     * @return list<self>
     */
    public static function assignableAsCoordinator(): array
    {
        return [
            self::Admin,
            self::Manager,
            self::Coordinator,
        ];
    }

    /**
     * @return list<string>
     */
    public static function assignableAsCoordinatorValues(): array
    {
        return array_map(
            static fn (self $role): string => $role->value,
            self::assignableAsCoordinator(),
        );
    }
}
