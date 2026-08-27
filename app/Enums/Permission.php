<?php

namespace App\Enums;

enum Permission: string
{
    case ClientsView = 'clients.view';
    case ClientsCreate = 'clients.create';
    case ClientsUpdateOwn = 'clients.update_own';
    case ClientsUpdateAny = 'clients.update_any';
    case ClientsDeleteOwn = 'clients.delete_own';
    case ClientsDeleteAny = 'clients.delete_any';
    case ClientsAssignCoordinator = 'clients.assign_coordinator';

    case CredentialsView = 'credentials.view';
    case CredentialsCreate = 'credentials.create';
    case CredentialsReveal = 'credentials.reveal';
    case CredentialsDelete = 'credentials.delete';

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

    /**
     * @return list<string>
     */
    public static function credentialValues(): array
    {
        return [
            self::CredentialsView->value,
            self::CredentialsCreate->value,
            self::CredentialsReveal->value,
            self::CredentialsDelete->value,
        ];
    }
}
