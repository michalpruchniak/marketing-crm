<?php

namespace App\Enums;

use App\Helpers\EnumHelper;

enum Permission: string
{
    use EnumHelper;

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
}
