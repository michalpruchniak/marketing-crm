<?php

use App\Enums\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function (User $user, int $id): bool {
    return $user->id === $id;
});

Broadcast::channel('leads', function (User $user): bool {
    return $user->can(Permission::LeadsView->value);
});
